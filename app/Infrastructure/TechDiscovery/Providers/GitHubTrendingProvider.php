<?php

namespace App\Infrastructure\TechDiscovery\Providers;

use App\Application\TechDiscovery\Contracts\TechTrendProviderInterface;
use App\Application\TechDiscovery\DTOs\TechTrendRecord;
use Carbon\CarbonImmutable;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class GitHubTrendingProvider extends AbstractTechTrendProvider implements TechTrendProviderInterface
{
    public function key(): string
    {
        return 'github_trending';
    }

    public function fetch(): array
    {
        $config = config('services.tech_discovery.providers.github_trending', []);
        $url = (string) ($config['url'] ?? 'https://github.com/trending?since=daily');
        $limit = (int) ($config['limit'] ?? 15);
        $html = $this->loadText($url);

        if (blank($html)) {
            return [];
        }

        $repos = array_slice($this->parseTrendingRepositories($html), 0, $limit);
        $records = [];

        foreach ($repos as $repo) {
            $fullName = $repo['full_name'];
            $metadata = $this->githubJson("https://api.github.com/repos/{$fullName}") ?? [];
            $languages = array_keys($this->githubJson("https://api.github.com/repos/{$fullName}/languages") ?? []);
            $topicsPayload = $this->githubJson("https://api.github.com/repos/{$fullName}/topics") ?? [];
            $topics = Arr::get($topicsPayload, 'names', []);
            $readme = $this->githubReadme($fullName);

            $description = $metadata['description'] ?? $repo['description'] ?? null;
            $readmeExcerpt = $this->excerpt($readme);
            $summary = $this->sentenceSummary($description, $readmeExcerpt);
            $howItWorks = $this->howItWorksFromReadme($readme, $description);
            $language = $metadata['language'] ?? $repo['language'] ?? ($languages[0] ?? null);

            $records[] = new TechTrendRecord(
                type: 'repository',
                source: $this->key(),
                sourceUrl: $url,
                externalId: $fullName,
                title: $fullName,
                url: (string) ($metadata['html_url'] ?? "https://github.com/{$fullName}"),
                description: $description,
                readmeExcerpt: $readmeExcerpt,
                summary: $summary,
                howItWorks: $howItWorks,
                technologies: $this->detectTechnologies($readmeExcerpt, $languages, $topics),
                language: $language,
                topics: is_array($topics) ? $topics : [],
                stars: isset($metadata['stargazers_count']) ? (int) $metadata['stargazers_count'] : $repo['stars'],
                forks: isset($metadata['forks_count']) ? (int) $metadata['forks_count'] : null,
                starsToday: $repo['stars_today'],
                publishedAt: isset($metadata['created_at']) ? CarbonImmutable::parse($metadata['created_at']) : null,
                trendDate: CarbonImmutable::today(),
                rawPayload: [
                    'trending' => $repo,
                    'repository' => $metadata,
                    'languages' => $languages,
                    'topics' => $topics,
                ],
            );
        }

        return $records;
    }

    private function githubHttp(): PendingRequest
    {
        $request = $this->http()
            ->acceptJson()
            ->withHeaders([
                'Accept' => 'application/vnd.github+json',
                'X-GitHub-Api-Version' => '2022-11-28',
            ]);

        $token = config('services.tech_discovery.github_token');
        if (filled($token)) {
            $request = $request->withToken((string) $token);
        }

        return $request;
    }

    private function githubJson(string $url): ?array
    {
        try {
            $response = $this->githubHttp()->get($url);

            return $response->successful() ? $response->json() : null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function githubReadme(string $fullName): ?string
    {
        try {
            $response = $this->githubHttp()
                ->withHeaders(['Accept' => 'application/vnd.github.raw'])
                ->get("https://api.github.com/repos/{$fullName}/readme");

            if ($response->successful()) {
                return $response->body();
            }
        } catch (\Throwable) {
            return null;
        }

        return null;
    }

    private function parseTrendingRepositories(string $html): array
    {
        $document = new DOMDocument;
        libxml_use_internal_errors(true);
        $document->loadHTML($html);
        libxml_clear_errors();

        $xpath = new DOMXPath($document);
        $articles = $xpath->query('//article[contains(concat(" ", normalize-space(@class), " "), " Box-row ")]');
        $repos = [];

        foreach ($articles ?: [] as $article) {
            if (! $article instanceof DOMElement) {
                continue;
            }

            $link = $xpath->query('.//h2//a', $article)->item(0);
            if (! $link instanceof DOMElement) {
                continue;
            }

            $path = trim($link->getAttribute('href'), '/ ');
            if (! str_contains($path, '/')) {
                continue;
            }

            $descriptionNode = $xpath->query('.//p', $article)->item(0);
            $languageNode = $xpath->query('.//*[@itemprop="programmingLanguage"]', $article)->item(0);
            $articleText = preg_replace('/\s+/', ' ', $article->textContent) ?? '';

            $repos[] = [
                'full_name' => $path,
                'description' => $descriptionNode ? trim($descriptionNode->textContent) : null,
                'language' => $languageNode ? trim($languageNode->textContent) : null,
                'stars' => $this->parseStars($articleText),
                'stars_today' => $this->parseStarsToday($articleText),
            ];
        }

        return $repos;
    }

    private function parseStars(string $text): ?int
    {
        if (preg_match('/\s([0-9][0-9,.kK]*)\s+stars?/i', $text, $matches)) {
            return $this->compactNumber($matches[1]);
        }

        return null;
    }

    private function parseStarsToday(string $text): ?int
    {
        if (preg_match('/([0-9][0-9,.kK]*)\s+stars?\s+today/i', $text, $matches)) {
            return $this->compactNumber($matches[1]);
        }

        return null;
    }

    private function compactNumber(string $value): ?int
    {
        $value = trim(Str::lower(str_replace(',', '', $value)));
        if ($value === '') {
            return null;
        }

        if (str_ends_with($value, 'k')) {
            return (int) round(((float) rtrim($value, 'k')) * 1000);
        }

        return is_numeric($value) ? (int) $value : null;
    }
}

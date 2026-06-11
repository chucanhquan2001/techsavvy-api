<?php

namespace App\Infrastructure\TechDiscovery\Providers;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

abstract class AbstractTechTrendProvider
{
    protected function http(): PendingRequest
    {
        return Http::timeout((int) config('services.tech_discovery.timeout_seconds', 15))
            ->retry((int) config('services.tech_discovery.retry_times', 1), 300)
            ->withHeaders([
                'User-Agent' => (string) config('services.tech_discovery.user_agent', 'TechSavvyBot/1.0'),
            ]);
    }

    protected function loadText(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        try {
            $response = $this->http()->get($url);

            return $response->successful() ? $response->body() : null;
        } catch (\Throwable) {
            return null;
        }
    }

    protected function readableText(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        $value = html_entity_decode((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $value = preg_replace('/```.*?```/s', ' ', $value) ?? $value;
        $value = preg_replace('/`([^`]+)`/', '$1', $value) ?? $value;
        $value = preg_replace('/!\[[^\]]*]\([^)]+\)/', ' ', $value) ?? $value;
        $value = preg_replace('/\[([^\]]+)]\([^)]+\)/', '$1', $value) ?? $value;
        $value = preg_replace('/<[^>]+>/', ' ', $value) ?? $value;
        $value = preg_replace('/[#*_~>|-]+/', ' ', $value) ?? $value;
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;
        $value = trim($value);

        return $value === '' ? null : $value;
    }

    protected function excerpt(?string $value, int $limit = 4000): ?string
    {
        $text = $this->readableText($value);

        return $text ? Str::limit($text, $limit, '') : null;
    }

    protected function sentenceSummary(?string $primary, ?string $fallback = null): ?string
    {
        $text = $this->readableText($primary) ?? $this->readableText($fallback);
        if (! $text) {
            return null;
        }

        $sentences = preg_split('/(?<=[.!?])\s+/', $text, 3) ?: [];
        $summary = trim(implode(' ', array_slice($sentences, 0, 2)));

        return Str::limit($summary !== '' ? $summary : $text, 500, '');
    }

    protected function howItWorksFromReadme(?string $readme, ?string $description): ?string
    {
        $text = $this->readableText($readme);

        if (! $text) {
            return $this->sentenceSummary($description);
        }

        foreach (['How it works', 'Usage', 'Quick start', 'Getting started', 'Architecture', 'Installation'] as $heading) {
            $pattern = '/'.$heading.'(.{80,900})/i';
            if (preg_match($pattern, $text, $matches)) {
                return Str::limit(trim($matches[0]), 800, '');
            }
        }

        return Str::limit($text, 800, '');
    }

    protected function detectTechnologies(?string $text, array $languages = [], array $topics = []): array
    {
        $haystack = strtolower(($text ?? '').' '.implode(' ', $languages).' '.implode(' ', $topics));
        $technologies = array_values(array_filter(array_merge($languages, $topics)));

        $keywords = [
            'AI' => ['ai', 'artificial intelligence', 'machine learning'],
            'LLM' => ['llm', 'large language model', 'openai', 'anthropic', 'ollama'],
            'RAG' => ['rag', 'retrieval augmented generation', 'vector database'],
            'Laravel' => ['laravel'],
            'PHP' => ['php'],
            'Vue' => ['vue'],
            'React' => ['react'],
            'Next.js' => ['next.js', 'nextjs'],
            'Node.js' => ['node.js', 'nodejs'],
            'Python' => ['python'],
            'TypeScript' => ['typescript'],
            'Docker' => ['docker'],
            'Kubernetes' => ['kubernetes', 'k8s'],
            'PostgreSQL' => ['postgresql', 'postgres'],
            'MySQL' => ['mysql'],
            'Redis' => ['redis'],
            'Security' => ['security', 'cve', 'vulnerability'],
            'DevOps' => ['devops', 'ci/cd', 'github actions'],
        ];

        foreach ($keywords as $technology => $needles) {
            foreach ($needles as $needle) {
                if (str_contains($haystack, $needle)) {
                    $technologies[] = $technology;
                    break;
                }
            }
        }

        return array_slice(array_values(array_unique(array_filter($technologies))), 0, 20);
    }
}

<?php

namespace Tests\Feature;

use App\Application\TechDiscovery\UseCases\SyncTechTrendsUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TechTrendSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_syncs_github_trending_repositories_and_feed_articles(): void
    {
        config([
            'services.tech_discovery.providers.github_trending.url' => 'https://example.test/github-trending',
            'services.tech_discovery.providers.github_trending.limit' => 1,
            'services.tech_discovery.feeds' => [
                'example_feed' => [
                    'url' => 'https://example.test/feed.xml',
                ],
            ],
            'services.tech_discovery.feed_limit_per_source' => 1,
            'services.tech_discovery.timeout_seconds' => 3,
            'services.tech_discovery.retry_times' => 1,
        ]);

        Http::fake([
            'https://example.test/github-trending' => Http::response($this->githubTrendingHtml(), 200),
            'https://api.github.com/repos/acme/demo-tool' => Http::response([
                'full_name' => 'acme/demo-tool',
                'html_url' => 'https://github.com/acme/demo-tool',
                'description' => 'A Laravel and AI developer tool.',
                'language' => 'PHP',
                'stargazers_count' => 1200,
                'forks_count' => 80,
                'created_at' => '2026-06-01T00:00:00Z',
            ], 200),
            'https://api.github.com/repos/acme/demo-tool/languages' => Http::response([
                'PHP' => 10000,
                'TypeScript' => 5000,
            ], 200),
            'https://api.github.com/repos/acme/demo-tool/topics' => Http::response([
                'names' => ['laravel', 'ai', 'developer-tools'],
            ], 200),
            'https://api.github.com/repos/acme/demo-tool/readme' => Http::response(
                "# Demo Tool\n\nDemo Tool helps teams build AI workflows.\n\n## How it works\n\nIt indexes project data, retrieves context, and generates useful summaries.",
                200
            ),
            'https://example.test/feed.xml' => Http::response($this->rssFeed(), 200),
        ]);

        $result = app(SyncTechTrendsUseCase::class)->execute();

        $this->assertSame(2, $result['records_count']);
        $this->assertSame(2, $result['saved_count']);
        $this->assertSame([], $result['failed_providers']);
        $this->assertDatabaseHas('tech_trend_items', [
            'type' => 'repository',
            'source' => 'github_trending',
            'external_id' => 'acme/demo-tool',
            'language' => 'PHP',
            'stars' => 1200,
        ]);
        $this->assertDatabaseHas('tech_trend_items', [
            'type' => 'article',
            'source' => 'example_feed',
            'external_id' => 'https://example.test/articles/ai-platform',
            'title' => 'New AI Platform Released',
        ]);
    }

    public function test_api_returns_paginated_tech_trends(): void
    {
        \App\Models\TechTrendItem::query()->create([
            'type' => 'repository',
            'source' => 'github_trending',
            'source_url' => 'https://github.com/trending?since=daily',
            'external_id' => 'acme/demo-tool',
            'title' => 'acme/demo-tool',
            'slug' => 'acme-demo-tool-github-trending-2026-06-11',
            'url' => 'https://github.com/acme/demo-tool',
            'summary' => 'A useful developer tool.',
            'technologies' => ['Laravel', 'AI'],
            'topics' => ['developer-tools'],
            'content_hash' => hash('sha256', 'demo'),
            'trend_date' => '2026-06-11',
            'status' => 'ready',
        ]);

        $response = $this->getJson('/api/tech-trends?technology=Laravel');

        $response->assertOk()
            ->assertJsonPath('status', 'ok')
            ->assertJsonPath('data.data.0.external_id', 'acme/demo-tool');
    }

    private function githubTrendingHtml(): string
    {
        return <<<'HTML'
        <html>
            <body>
                <article class="Box-row">
                    <h2><a href="/acme/demo-tool">acme / demo-tool</a></h2>
                    <p>A developer tool for AI teams.</p>
                    <span itemprop="programmingLanguage">PHP</span>
                    <span>120 stars today</span>
                </article>
            </body>
        </html>
        HTML;
    }

    private function rssFeed(): string
    {
        return <<<'XML'
        <?xml version="1.0" encoding="UTF-8"?>
        <rss version="2.0">
            <channel>
                <title>Example Feed</title>
                <item>
                    <title>New AI Platform Released</title>
                    <link>https://example.test/articles/ai-platform</link>
                    <guid>https://example.test/articles/ai-platform</guid>
                    <description>A practical look at new AI developer tooling.</description>
                    <pubDate>Thu, 11 Jun 2026 02:00:00 +0000</pubDate>
                    <category>AI</category>
                    <category>developer-tools</category>
                </item>
            </channel>
        </rss>
        XML;
    }
}

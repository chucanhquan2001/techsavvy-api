<?php

namespace App\Infrastructure\TechDiscovery\Providers;

use App\Application\TechDiscovery\Contracts\TechTrendProviderInterface;
use App\Application\TechDiscovery\DTOs\TechTrendRecord;
use Carbon\CarbonImmutable;
use SimpleXMLElement;

class TechNewsFeedProvider extends AbstractTechTrendProvider implements TechTrendProviderInterface
{
    public function key(): string
    {
        return 'tech_news_feeds';
    }

    public function fetch(): array
    {
        $feeds = config('services.tech_discovery.feeds', []);
        $limitPerFeed = (int) config('services.tech_discovery.feed_limit_per_source', 10);
        $records = [];

        foreach ($feeds as $source => $feed) {
            $url = (string) ($feed['url'] ?? '');
            $xml = $this->loadText($url);

            if (blank($xml)) {
                continue;
            }

            foreach (array_slice($this->parseFeedItems($xml), 0, $limitPerFeed) as $item) {
                $title = $item['title'] ?? null;
                $link = $item['link'] ?? null;

                if (blank($title) || blank($link)) {
                    continue;
                }

                $description = $this->excerpt($item['description'] ?? null, 1200);
                $publishedAt = $this->parseDate($item['published_at'] ?? null);
                $text = trim(($title ?? '').' '.($description ?? '').' '.implode(' ', $item['categories'] ?? []));

                $records[] = new TechTrendRecord(
                    type: 'article',
                    source: (string) $source,
                    sourceUrl: $url,
                    externalId: (string) ($item['guid'] ?? $link),
                    title: (string) $title,
                    url: (string) $link,
                    description: $description,
                    summary: $this->sentenceSummary($description, $title),
                    howItWorks: null,
                    technologies: $this->detectTechnologies($text, [], $item['categories'] ?? []),
                    topics: $item['categories'] ?? [],
                    publishedAt: $publishedAt,
                    trendDate: $publishedAt ?? CarbonImmutable::today(),
                    rawPayload: $item,
                );
            }
        }

        return $records;
    }

    private function parseFeedItems(string $xml): array
    {
        libxml_use_internal_errors(true);
        $feed = simplexml_load_string($xml);
        libxml_clear_errors();

        if (! $feed instanceof SimpleXMLElement) {
            return [];
        }

        if (isset($feed->channel->item)) {
            return $this->parseRssItems($feed);
        }

        if ($feed->getName() === 'feed' && isset($feed->entry)) {
            return $this->parseAtomItems($feed);
        }

        return [];
    }

    private function parseRssItems(SimpleXMLElement $feed): array
    {
        $items = [];

        foreach ($feed->channel->item as $item) {
            $categories = [];
            foreach ($item->category as $category) {
                $categories[] = trim((string) $category);
            }

            $items[] = [
                'title' => trim((string) $item->title),
                'link' => trim((string) $item->link),
                'guid' => trim((string) $item->guid) ?: null,
                'description' => trim((string) $item->description),
                'published_at' => trim((string) $item->pubDate) ?: null,
                'categories' => array_values(array_filter($categories)),
            ];
        }

        return $items;
    }

    private function parseAtomItems(SimpleXMLElement $feed): array
    {
        $items = [];

        foreach ($feed->entry as $entry) {
            $link = '';
            foreach ($entry->link as $candidate) {
                $attributes = $candidate->attributes();
                if ((string) ($attributes['rel'] ?? 'alternate') === 'alternate') {
                    $link = (string) ($attributes['href'] ?? '');
                    break;
                }
            }

            $categories = [];
            foreach ($entry->category as $category) {
                $attributes = $category->attributes();
                $categories[] = (string) ($attributes['term'] ?? '');
            }

            $items[] = [
                'title' => trim((string) $entry->title),
                'link' => trim($link),
                'guid' => trim((string) $entry->id) ?: null,
                'description' => trim((string) ($entry->summary ?: $entry->content)),
                'published_at' => trim((string) ($entry->published ?: $entry->updated)) ?: null,
                'categories' => array_values(array_filter($categories)),
            ];
        }

        return $items;
    }

    private function parseDate(?string $value): ?CarbonImmutable
    {
        if (blank($value)) {
            return null;
        }

        try {
            return CarbonImmutable::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }
}

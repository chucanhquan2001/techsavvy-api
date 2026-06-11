<?php

namespace App\Infrastructure\TechDiscovery;

use App\Application\TechDiscovery\DTOs\TechTrendRecord;
use App\Models\TechTrendItem;
use Illuminate\Support\Str;

class TechTrendPersistenceService
{
    /**
     * @param  array<TechTrendRecord>  $records
     */
    public function persist(array $records): int
    {
        $saved = 0;

        foreach ($records as $record) {
            $trendDate = $record->trendDate?->toDateString()
                ?? $record->publishedAt?->toDateString()
                ?? now()->toDateString();

            TechTrendItem::query()->updateOrCreate(
                [
                    'source' => $record->source,
                    'external_id' => $record->externalId,
                    'trend_date' => $trendDate,
                ],
                [
                    'type' => $record->type,
                    'source_url' => $record->sourceUrl,
                    'title' => $record->title,
                    'slug' => $this->slugFor($record, $trendDate),
                    'url' => $record->url,
                    'description' => $record->description,
                    'readme_excerpt' => $record->readmeExcerpt,
                    'summary' => $record->summary,
                    'how_it_works' => $record->howItWorks,
                    'technologies' => array_values(array_unique(array_filter($record->technologies))),
                    'language' => $record->language,
                    'topics' => array_values(array_unique(array_filter($record->topics))),
                    'stars' => $record->stars,
                    'forks' => $record->forks,
                    'stars_today' => $record->starsToday,
                    'published_at' => $record->publishedAt,
                    'raw_payload' => $record->rawPayload,
                    'content_hash' => $record->contentHash(),
                    'status' => $record->status,
                ],
            );

            $saved++;
        }

        return $saved;
    }

    private function slugFor(TechTrendRecord $record, string $trendDate): string
    {
        return Str::slug($record->title.'-'.$record->source.'-'.$trendDate);
    }
}

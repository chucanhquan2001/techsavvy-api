<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TechTrendItem extends Model
{
    protected $fillable = [
        'type',
        'source',
        'source_url',
        'external_id',
        'title',
        'slug',
        'url',
        'description',
        'readme_excerpt',
        'summary',
        'how_it_works',
        'technologies',
        'language',
        'topics',
        'stars',
        'forks',
        'stars_today',
        'published_at',
        'trend_date',
        'raw_payload',
        'content_hash',
        'status',
    ];

    protected $hidden = [
        'raw_payload',
        'content_hash',
    ];

    protected $casts = [
        'technologies' => 'array',
        'topics' => 'array',
        'raw_payload' => 'array',
        'stars' => 'integer',
        'forks' => 'integer',
        'stars_today' => 'integer',
        'published_at' => 'datetime',
        'trend_date' => 'date',
    ];

    /**
     * @param  array<string, mixed>  $filters
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $status = $filters['status'] ?? null;
        if ($status === null || $status === '') {
            $query->where('status', 'ready');
        } else {
            $query->where('status', (string) $status);
        }

        return $query
            ->when(! empty($filters['type']), fn (Builder $q) => $q->where('type', (string) $filters['type']))
            ->when(! empty($filters['source']), fn (Builder $q) => $q->where('source', (string) $filters['source']))
            ->when(! empty($filters['date']), fn (Builder $q) => $q->whereDate('trend_date', $filters['date']))
            ->when(! empty($filters['from']), fn (Builder $q) => $q->whereDate('trend_date', '>=', $filters['from']))
            ->when(! empty($filters['to']), fn (Builder $q) => $q->whereDate('trend_date', '<=', $filters['to']))
            ->when(! empty($filters['q']), function (Builder $q) use ($filters) {
                $term = '%'.(string) $filters['q'].'%';
                $q->where(function (Builder $nested) use ($term) {
                    $nested->where('title', 'like', $term)
                        ->orWhere('summary', 'like', $term);
                });
            })
            ->when(! empty($filters['technology']), function (Builder $q) use ($filters) {
                $technology = (string) $filters['technology'];
                $q->where(function (Builder $nested) use ($technology) {
                    $nested->whereJsonContains('technologies', $technology)
                        ->orWhereJsonContains('topics', $technology)
                        ->orWhere('language', $technology);
                });
            });
    }
}

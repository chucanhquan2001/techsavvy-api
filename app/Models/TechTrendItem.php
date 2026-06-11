<?php

namespace App\Models;

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
}

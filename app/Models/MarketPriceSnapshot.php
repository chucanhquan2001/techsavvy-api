<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MarketPriceSnapshot extends Model
{
    protected $table = 'market_price_snapshots';

    protected $fillable = [
        'instrument',
        'category',
        'value',
        'currency',
        'unit',
        'quoted_at',
        'source',
        'source_type',
    ];

    protected $casts = [
        'value' => 'decimal:8',
        'quoted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * @param  array<string, mixed>  $filters
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when(! empty($filters['category']), fn (Builder $q) => $q->where('category', (string) $filters['category']))
            ->when(! empty($filters['instrument']), fn (Builder $q) => $q->where('instrument', (string) $filters['instrument']))
            ->when(! empty($filters['currency']), fn (Builder $q) => $q->where('currency', (string) $filters['currency']))
            ->when(! empty($filters['source']), fn (Builder $q) => $q->where('source', (string) $filters['source']));
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MarketPriceHistory extends Model
{
    protected $table = 'market_price_histories';

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
            ->when(! empty($filters['source']), fn (Builder $q) => $q->where('source', (string) $filters['source']))
            ->when(! empty($filters['from']), fn (Builder $q) => $q->whereDate('quoted_at', '>=', $filters['from']))
            ->when(! empty($filters['to']), fn (Builder $q) => $q->whereDate('quoted_at', '<=', $filters['to']));
    }
}

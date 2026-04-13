<?php

namespace App\Models;

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
}

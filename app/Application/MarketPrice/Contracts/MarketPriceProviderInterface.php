<?php

namespace App\Application\MarketPrice\Contracts;

use App\Application\MarketPrice\DTOs\MarketPriceRecord;

interface MarketPriceProviderInterface
{
    public function key(): string;

    /**
     * @return array<MarketPriceRecord>
     */
    public function fetch(): array;
}

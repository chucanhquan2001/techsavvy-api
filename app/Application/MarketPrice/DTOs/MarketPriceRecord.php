<?php

namespace App\Application\MarketPrice\DTOs;

final class MarketPriceRecord
{
    public function __construct(
        public readonly string $instrument,
        public readonly string $category,
        public readonly float $value,
        public readonly string $currency,
        public readonly ?string $unit,
        public readonly \DateTimeImmutable $quotedAt,
        public readonly string $source,
        public readonly string $sourceType,
    ) {}

    public function toArray(): array
    {
        return [
            'instrument' => $this->instrument,
            'category' => $this->category,
            'value' => $this->value,
            'currency' => $this->currency,
            'unit' => $this->unit,
            'quoted_at' => $this->quotedAt->format('Y-m-d H:i:s'),
            'source' => $this->source,
            'source_type' => $this->sourceType,
        ];
    }
}

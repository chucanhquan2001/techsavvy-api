<?php

namespace App\Infrastructure\MarketPrice\Providers;

use App\Application\MarketPrice\Contracts\MarketPriceProviderInterface;
use App\Application\MarketPrice\DTOs\MarketPriceRecord;

class FuelPriceProvider extends AbstractHybridProvider implements MarketPriceProviderInterface
{
    public function key(): string
    {
        return 'fuel';
    }

    public function fetch(): array
    {
        $config = config('services.market_data.providers.fuel', []);
        $quotedAt = new \DateTimeImmutable();
        $sourceType = 'api';

        $json = $this->loadJson($config['api_url'] ?? null);
        $ron95 = $this->toFloat(data_get($json, 'ron95'));
        $diesel = $this->toFloat(data_get($json, 'diesel'));

        if ($ron95 === null && $diesel === null) {
            $sourceType = 'scrape';
            $html = $this->loadHtml($config['scrape_url'] ?? null);
            $ron95 = $this->numberFromHtml($html, '/ron95[^0-9]*([0-9]+(?:[.,][0-9]+)?)/i');
            $diesel = $this->numberFromHtml($html, '/diesel[^0-9]*([0-9]+(?:[.,][0-9]+)?)/i');
        }

        $records = [];

        if ($ron95 !== null) {
            $records[] = new MarketPriceRecord(
                instrument: 'RON95_VN',
                category: 'fuel',
                value: $ron95,
                currency: 'VND',
                unit: '1 liter',
                quotedAt: $quotedAt,
                source: (string) ($config['name'] ?? 'fuel'),
                sourceType: $sourceType,
            );
        }

        if ($diesel !== null) {
            $records[] = new MarketPriceRecord(
                instrument: 'DIESEL_VN',
                category: 'fuel',
                value: $diesel,
                currency: 'VND',
                unit: '1 liter',
                quotedAt: $quotedAt,
                source: (string) ($config['name'] ?? 'fuel'),
                sourceType: $sourceType,
            );
        }

        return $records;
    }
}

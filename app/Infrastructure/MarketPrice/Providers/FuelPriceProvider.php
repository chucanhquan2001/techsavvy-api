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
            $ron95 = $this->extractVndPrice($html, '/x[aă]ng\s*ron\s*95[^0-9]*([0-9][0-9.,]*)/iu');
            $diesel = $this->extractVndPrice($html, '/d[aà]u\s*do[^0-9]*([0-9][0-9.,]*)/iu');
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

    private function extractVndPrice(?string $html, string $pattern): ?float
    {
        if (blank($html) || ! preg_match($pattern, $html, $matches)) {
            return null;
        }

        $digitsOnly = preg_replace('/\D+/', '', $matches[1] ?? '');
        if ($digitsOnly === null || $digitsOnly === '') {
            return null;
        }

        return (float) $digitsOnly;
    }
}

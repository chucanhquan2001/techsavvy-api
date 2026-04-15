<?php

namespace App\Infrastructure\MarketPrice\Providers;

use App\Application\MarketPrice\Contracts\MarketPriceProviderInterface;
use App\Application\MarketPrice\DTOs\MarketPriceRecord;

class SilverPriceProvider extends AbstractHybridProvider implements MarketPriceProviderInterface
{
    public function key(): string
    {
        return 'silver';
    }

    public function fetch(): array
    {
        $config = config('services.market_data.providers.silver', []);
        $quotedAt = new \DateTimeImmutable();
        $price = null;
        $sourceType = 'api';

        $json = $this->loadJson($config['api_url'] ?? null);
        $price = $this->toFloat(data_get($json, 'rates.XAG'));

        if ($price !== null && $price > 0.0) {
            $price = 1 / $price;
        }

        if ($price === null) {
            $sourceType = 'scrape';
            $price = $this->numberFromHtml($this->loadHtml($config['scrape_url'] ?? null));
        }

        if ($price === null) {
            return [];
        }

        return [
            new MarketPriceRecord(
                instrument: 'XAGUSD',
                category: 'silver',
                value: $price,
                currency: 'USD',
                unit: '1 oz',
                quotedAt: $quotedAt,
                source: (string) ($config['name'] ?? 'silver'),
                sourceType: $sourceType,
            ),
        ];
    }
}

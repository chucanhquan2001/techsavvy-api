<?php

namespace App\Infrastructure\MarketPrice\Providers;

use App\Application\MarketPrice\Contracts\MarketPriceProviderInterface;
use App\Application\MarketPrice\DTOs\MarketPriceRecord;

class VietnamGoldPriceProvider extends AbstractHybridProvider implements MarketPriceProviderInterface
{
    public function key(): string
    {
        return 'vn_gold';
    }

    public function fetch(): array
    {
        $config = config('services.market_data.providers.vn_gold', []);
        $quotedAt = new \DateTimeImmutable();
        $sourceType = 'api';

        $json = $this->loadJson($config['api_url'] ?? null);
        $sjcBuy = $this->toFloat(data_get($json, 'sjc.buy'));
        $sjcSell = $this->toFloat(data_get($json, 'sjc.sell'));

        if ($sjcBuy === null && $sjcSell === null) {
            $sourceType = 'scrape';
            $html = $this->loadHtml($config['scrape_url'] ?? null);
            $sjcBuy = $this->numberFromHtml($html, '/sjc[^0-9]*mua[^0-9]*([0-9]+(?:[.,][0-9]+)?)/i');
            $sjcSell = $this->numberFromHtml($html, '/sjc[^0-9]*ban[^0-9]*([0-9]+(?:[.,][0-9]+)?)/i');
        }

        $records = [];

        if ($sjcBuy !== null) {
            $records[] = new MarketPriceRecord(
                instrument: 'SJC_BUY',
                category: 'gold_vn',
                value: $sjcBuy,
                currency: 'VND',
                unit: '1 luong',
                quotedAt: $quotedAt,
                source: (string) ($config['name'] ?? 'vn_gold'),
                sourceType: $sourceType,
            );
        }

        if ($sjcSell !== null) {
            $records[] = new MarketPriceRecord(
                instrument: 'SJC_SELL',
                category: 'gold_vn',
                value: $sjcSell,
                currency: 'VND',
                unit: '1 luong',
                quotedAt: $quotedAt,
                source: (string) ($config['name'] ?? 'vn_gold'),
                sourceType: $sourceType,
            );
        }

        return $records;
    }
}

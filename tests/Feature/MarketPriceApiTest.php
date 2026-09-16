<?php

namespace Tests\Feature;

use App\Models\MarketPriceHistory;
use App\Models\MarketPriceSnapshot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketPriceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_lists_market_price_snapshots_with_filters(): void
    {
        $this->seedSnapshots();

        $response = $this->getJson('/api/market-prices?category=crypto&per_page=10');

        $response->assertOk()
            ->assertJsonPath('status', 'ok')
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('meta.per_page', 10)
            ->assertJsonPath('data.0.instrument', 'BTCUSD')
            ->assertJsonPath('data.0.category', 'crypto');
    }

    public function test_api_shows_latest_snapshot_by_instrument(): void
    {
        $this->seedSnapshots();

        $response = $this->getJson('/api/market-prices/BTCUSD');

        $response->assertOk()
            ->assertJsonPath('status', 'ok')
            ->assertJsonPath('data.instrument', 'BTCUSD')
            ->assertJsonPath('data.value', '65000.50000000');
    }

    public function test_api_returns_404_for_unknown_instrument(): void
    {
        $response = $this->getJson('/api/market-prices/UNKNOWN');

        $response->assertStatus(404)
            ->assertJsonPath('status', 'fail')
            ->assertJsonPath('message', 'Market price not found');
    }

    public function test_api_returns_paginated_history_for_instrument(): void
    {
        MarketPriceHistory::query()->create([
            'instrument' => 'BTCUSD',
            'category' => 'crypto',
            'value' => 64000,
            'currency' => 'USD',
            'unit' => '1 BTC',
            'quoted_at' => '2026-09-15 10:00:00',
            'source' => 'bitcoin-test',
            'source_type' => 'api',
        ]);
        MarketPriceHistory::query()->create([
            'instrument' => 'BTCUSD',
            'category' => 'crypto',
            'value' => 65000.5,
            'currency' => 'USD',
            'unit' => '1 BTC',
            'quoted_at' => '2026-09-16 10:00:00',
            'source' => 'bitcoin-test',
            'source_type' => 'api',
        ]);
        MarketPriceHistory::query()->create([
            'instrument' => 'XAUUSD',
            'category' => 'gold',
            'value' => 2400,
            'currency' => 'USD',
            'unit' => '1 oz',
            'quoted_at' => '2026-09-16 10:00:00',
            'source' => 'world-gold-test',
            'source_type' => 'api',
        ]);

        $response = $this->getJson('/api/market-prices/BTCUSD/history?per_page=1&from=2026-09-15&to=2026-09-16');

        $response->assertOk()
            ->assertJsonPath('status', 'ok')
            ->assertJsonPath('meta.total', 2)
            ->assertJsonPath('meta.per_page', 1)
            ->assertJsonPath('meta.last_page', 2)
            ->assertJsonPath('data.0.instrument', 'BTCUSD')
            ->assertJsonPath('data.0.value', '65000.50000000');
    }

    public function test_api_rejects_invalid_history_per_page(): void
    {
        $response = $this->getJson('/api/market-prices/BTCUSD/history?per_page=101');

        $response->assertStatus(422)
            ->assertJsonPath('status', 'fail');
    }

    private function seedSnapshots(): void
    {
        MarketPriceSnapshot::query()->create([
            'instrument' => 'BTCUSD',
            'category' => 'crypto',
            'value' => 65000.5,
            'currency' => 'USD',
            'unit' => '1 BTC',
            'quoted_at' => '2026-09-16 10:00:00',
            'source' => 'bitcoin-test',
            'source_type' => 'api',
        ]);

        MarketPriceSnapshot::query()->create([
            'instrument' => 'XAUUSD',
            'category' => 'gold',
            'value' => 2400.25,
            'currency' => 'USD',
            'unit' => '1 oz',
            'quoted_at' => '2026-09-16 10:00:00',
            'source' => 'world-gold-test',
            'source_type' => 'api',
        ]);
    }
}

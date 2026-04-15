<?php

namespace Tests\Feature;

use App\Application\MarketPrice\UseCases\SyncMarketPricesUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MarketPriceSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_persists_snapshot_and_history_from_api_sources(): void
    {
        $this->configureMarketSources();

        Http::fake([
            'https://example.test/api/bitcoin' => Http::response(['bitcoin' => ['usd' => 65000.5]], 200),
            'https://example.test/api/world-gold' => Http::response(['rates' => ['XAU' => 0.00042]], 200),
            'https://example.test/api/vn-gold' => Http::response(['sjc' => ['buy' => 91000000, 'sell' => 93000000]], 200),
            'https://example.test/api/silver' => Http::response(['rates' => ['XAG' => 0.035]], 200),
            'https://example.test/api/fuel' => Http::response(['ron95' => 23120, 'diesel' => 20100], 200),
        ]);

        $result = app(SyncMarketPricesUseCase::class)->execute();

        $this->assertSame(7, $result['records_count']);
        $this->assertSame([], $result['failed_providers']);
        $this->assertDatabaseCount('market_price_snapshots', 7);
        $this->assertDatabaseCount('market_price_histories', 7);
    }

    public function test_it_uses_scrape_fallback_when_api_is_unavailable(): void
    {
        $this->configureMarketSources();

        Http::fake([
            'https://example.test/api/bitcoin' => Http::response([], 500),
            'https://example.test/scrape/bitcoin' => Http::response('Price: 70500.25', 200),
            'https://example.test/api/world-gold' => Http::response(['rates' => ['XAU' => 0.0005]], 200),
            'https://example.test/api/vn-gold' => Http::response(['sjc' => ['buy' => 90000000, 'sell' => 92000000]], 200),
            'https://example.test/api/silver' => Http::response(['rates' => ['XAG' => 0.04]], 200),
            'https://example.test/api/fuel' => Http::response(['ron95' => 23000, 'diesel' => 20000], 200),
        ]);

        $result = app(SyncMarketPricesUseCase::class)->execute();

        $this->assertSame(7, $result['records_count']);
        $this->assertSame([], $result['failed_providers']);
        $this->assertDatabaseHas('market_price_snapshots', [
            'instrument' => 'BTCUSD',
            'source_type' => 'scrape',
            'source' => 'bitcoin-test',
        ]);
    }

    private function configureMarketSources(): void
    {
        config([
            'services.market_data.providers' => [
                'bitcoin' => [
                    'name' => 'bitcoin-test',
                    'api_url' => 'https://example.test/api/bitcoin',
                    'scrape_url' => 'https://example.test/scrape/bitcoin',
                ],
                'world_gold' => [
                    'name' => 'world-gold-test',
                    'api_url' => 'https://example.test/api/world-gold',
                    'scrape_url' => 'https://example.test/scrape/world-gold',
                ],
                'vn_gold' => [
                    'name' => 'vn-gold-test',
                    'api_url' => 'https://example.test/api/vn-gold',
                    'scrape_url' => 'https://example.test/scrape/vn-gold',
                ],
                'silver' => [
                    'name' => 'silver-test',
                    'api_url' => 'https://example.test/api/silver',
                    'scrape_url' => 'https://example.test/scrape/silver',
                ],
                'fuel' => [
                    'name' => 'fuel-test',
                    'api_url' => 'https://example.test/api/fuel',
                    'scrape_url' => 'https://example.test/scrape/fuel',
                ],
            ],
            'services.market_data.timeout_seconds' => 3,
            'services.market_data.retry_times' => 1,
        ]);
    }
}

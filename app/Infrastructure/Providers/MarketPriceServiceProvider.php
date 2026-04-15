<?php

namespace App\Infrastructure\Providers;

use App\Application\MarketPrice\UseCases\SyncMarketPricesUseCase;
use App\Infrastructure\MarketPrice\MarketPricePersistenceService;
use App\Infrastructure\MarketPrice\Providers\BitcoinPriceProvider;
use App\Infrastructure\MarketPrice\Providers\FuelPriceProvider;
use App\Infrastructure\MarketPrice\Providers\SilverPriceProvider;
use App\Infrastructure\MarketPrice\Providers\VietnamGoldPriceProvider;
use App\Infrastructure\MarketPrice\Providers\WorldGoldPriceProvider;
use Illuminate\Support\ServiceProvider;

class MarketPriceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SyncMarketPricesUseCase::class, function ($app): SyncMarketPricesUseCase {
            return new SyncMarketPricesUseCase(
                providers: [
                    $app->make(WorldGoldPriceProvider::class),
                    $app->make(VietnamGoldPriceProvider::class),
                    $app->make(BitcoinPriceProvider::class),
                    $app->make(SilverPriceProvider::class),
                    $app->make(FuelPriceProvider::class),
                ],
                persistenceService: $app->make(MarketPricePersistenceService::class),
            );
        });
    }
}

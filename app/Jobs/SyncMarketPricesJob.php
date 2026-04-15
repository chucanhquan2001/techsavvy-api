<?php

namespace App\Jobs;

use App\Application\MarketPrice\UseCases\SyncMarketPricesUseCase;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SyncMarketPricesJob implements ShouldQueue
{
    use Queueable;

    public function handle(SyncMarketPricesUseCase $useCase): void
    {
        $result = $useCase->execute();

        Log::info('Market price sync completed', $result);
    }
}

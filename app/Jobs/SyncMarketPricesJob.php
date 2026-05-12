<?php

namespace App\Jobs;

use App\Application\MarketPrice\UseCases\SyncMarketPricesUseCase;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncMarketPricesJob implements ShouldQueue
{
    use Queueable;

    public function handle(SyncMarketPricesUseCase $useCase): void
    {
        $result = $useCase->execute();

        Log::channel('job_info_stack')->info('Market price sync completed', $result);
    }

    public function failed(Throwable $e): void
    {
        Log::channel('job_alert_stack')->error('Market price sync failed', [
            'exception' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
    }
}

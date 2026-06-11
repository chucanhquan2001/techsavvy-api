<?php

namespace App\Jobs;

use App\Application\TechDiscovery\UseCases\SyncTechTrendsUseCase;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncTechTrendsJob implements ShouldQueue
{
    use Queueable;

    public function handle(SyncTechTrendsUseCase $useCase): void
    {
        $result = $useCase->execute();

        Log::channel('job_info_stack')->info('Tech trend sync completed', $result);
    }

    public function failed(Throwable $e): void
    {
        Log::channel('job_alert_stack')->error('Tech trend sync failed', [
            'exception' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
    }
}

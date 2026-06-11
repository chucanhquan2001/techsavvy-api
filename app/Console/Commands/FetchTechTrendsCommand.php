<?php

namespace App\Console\Commands;

use App\Jobs\SyncTechTrendsJob;
use Illuminate\Console\Command;

class FetchTechTrendsCommand extends Command
{
    protected $signature = 'tech:sync-trends';

    protected $description = 'Dispatch tech trend and source synchronization job';

    public function handle(): int
    {
        SyncTechTrendsJob::dispatch();
        $this->info('Tech trend sync job dispatched.');

        return self::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use App\Jobs\SyncMarketPricesJob;
use Illuminate\Console\Command;

class FetchMarketPricesCommand extends Command
{
    protected $signature = 'market:sync-prices';

    protected $description = 'Dispatch market price synchronization job';

    public function handle(): int
    {
        SyncMarketPricesJob::dispatch();
        $this->info('Market price sync job dispatched.');

        return self::SUCCESS;
    }
}

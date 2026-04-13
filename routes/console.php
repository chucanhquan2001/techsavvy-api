<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('market:sync-prices')
    ->timezone((string) config('app.timezone', 'Asia/Ho_Chi_Minh'))
    ->dailyAt((string) config('services.market_data.schedule_time', '07:00'))
    ->withoutOverlapping();

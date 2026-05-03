<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Commands — RF-20
|--------------------------------------------------------------------------
|
| These commands run automatically on the defined schedule.
| Raw PHP equivalent: cron jobs running PHP scripts.
|
| To activate scheduling on Windows (development):
| Run once in terminal: php artisan schedule:work
|
| In production (Linux server):
| Add to crontab: * * * * * php /path/to/artisan schedule:run
|
*/

// RF-20 — Check expiring licenses every day at 8:00 AM
Schedule::command('itam:check-expiring-licenses')
         ->dailyAt('08:00')
         ->withoutOverlapping()
         ->appendOutputTo(storage_path('logs/license-checks.log'));
<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

Schedule::command('scrap:article')->daily();

Schedule::call(function () {
    Artisan::call('queue:work', [
        '----stop-when-empty' => true,
        '--timeout' => 60,
        '--tries' => 1,
    ]);
})->everyMinute();

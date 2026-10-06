<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Dijalankan oleh loop scheduler dalam container (lihat scripts/00-laravel-deploy.sh),
// jadi reminder tak lagi bergantung 100% pada cron luar yang perlukan CRON_TOKEN.
Schedule::command('reminders:send-due')->everyMinute();
Schedule::command('classes:send-upcoming')->everyMinute();

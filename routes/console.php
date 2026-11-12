<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\DatabaseBackupService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Automatically snapshot database nightly at 02:00 AM
Schedule::call(function () {
    app(DatabaseBackupService::class)->createBackup();
})->dailyAt('02:00');
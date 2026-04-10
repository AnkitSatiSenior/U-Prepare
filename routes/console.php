<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Actions\NotifyExpiredSecuritiesAction;
use App\Actions\NotifyExpiredSecuritiesWhatsAppAction;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
 |--------------------------------------------------------------------------
 | Email Notifications
 |--------------------------------------------------------------------------
 | Scans for expired securities and dispatches Email jobs.
 */
Schedule::call(function () {
    app(NotifyExpiredSecuritiesWhatsAppAction::class)->execute();
})
    ->dailyAt('10:00') // Staggered execution
    ->name('contracts:notify-expired-securities-whatsapp') // Unique Mutex Name
    ->withoutOverlapping()
    ->onOneServer();
Schedule::call(function () {
    app(NotifyExpiredSecuritiesAction::class)->execute();
})
    ->dailyAt('08:00')
    ->name('contracts:notify-expired-securities-email') // Unique Mutex Name
    ->withoutOverlapping()
    ->onOneServer();

/*
 |--------------------------------------------------------------------------
 | WhatsApp Notifications
 |--------------------------------------------------------------------------
 | Scans for expired securities and dispatches WhatsApp jobs.
 | Staggered by 5 minutes to prevent database read contention.
 */

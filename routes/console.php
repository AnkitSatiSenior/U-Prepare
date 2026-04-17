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
 | ESCALATION ENGINE — AUTO SCHEDULER
 |--------------------------------------------------------------------------
 | Runs every day at 09:00 AM.
 | Evaluates ALL 4 categories:
 |   1. Social Safeguard   — Pre-Construction pending while During Construction started
 |   2. Physical Progress  — No BOQ/EPC entries in expected timeframe
 |   3. Financial Progress — No bill submission in expected interval
 |   4. Contract Security  — Certificate near expiry or already expired
 |
 | Sends real emails + WhatsApp to all mapped users at the correct level.
 | Idempotency locks prevent duplicate alerts for the same violation.
 */
Schedule::command('escalation:run')
    ->dailyAt('09:00')
    ->name('escalation:full-engine')
    ->withoutOverlapping()   // Never run two instances at the same time
    ->onOneServer()          // If you have multiple servers, only one runs it
    ->runInBackground();     // Does not block other scheduled tasks


/*
 |--------------------------------------------------------------------------
 | LEGACY: Expired Securities — WhatsApp (kept as-is)
 |--------------------------------------------------------------------------
 | NOTE: The new escalation engine above handles near-expiry AND expired
 | security escalation at the hierarchy level. This legacy action sends a
 | simpler, non-hierarchical WhatsApp blast to all project assignees.
 | You may disable it once the new engine is fully trusted.
 */
Schedule::call(function () {
    app(NotifyExpiredSecuritiesWhatsAppAction::class)->execute();
})
    ->dailyAt('10:00')
    ->name('contracts:notify-expired-securities-whatsapp')
    ->withoutOverlapping()
    ->onOneServer();

// Schedule::call(function () {
//     app(NotifyExpiredSecuritiesAction::class)->execute();
// })
//     ->dailyAt('08:00')
//     ->name('contracts:notify-expired-securities-email')
//     ->withoutOverlapping()
//     ->onOneServer();
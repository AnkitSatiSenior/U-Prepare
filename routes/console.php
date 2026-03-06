<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Actions\NotifyExpiredSecuritiesAction;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');



Schedule::call(function () {
    app(NotifyExpiredSecuritiesAction::class)->execute();
})
    ->dailyAt('08:00')
    ->name('contracts:notify-expired-securities')
    ->withoutOverlapping()
    ->onOneServer();

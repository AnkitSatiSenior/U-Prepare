<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Escalation\EscalationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * RUN ESCALATION ENGINE — PRODUCTION COMMAND
 *
 * This is the command the Laravel Scheduler calls every day automatically.
 * It runs all 4 escalation categories and sends real emails + WhatsApp.
 *
 * To run manually:
 *   php artisan escalation:run
 *
 * Scheduled automatically in routes/console.php to run daily at 09:00 AM.
 */
class RunEscalationEngine extends Command
{
    protected $signature   = 'escalation:run';
    protected $description = 'Run the full escalation engine — evaluates all 4 categories and dispatches real notifications.';

    public function handle(EscalationService $service): int
    {
        $this->components->info('Starting Escalation Engine — ' . now()->toDateTimeString());

        Log::info('[EscalationEngine] Daily run started.', ['at' => now()->toDateTimeString()]);

        try {
            $service->runFullEngine($this);  // pass $this so it can call $console->info()

            $this->components->info('Escalation Engine completed successfully.');
            Log::info('[EscalationEngine] Daily run completed successfully.');

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->components->error('Escalation Engine FAILED: ' . $e->getMessage());
            Log::error('[EscalationEngine] Daily run FAILED: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return self::FAILURE;
        }
    }
}
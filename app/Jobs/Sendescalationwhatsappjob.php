<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * SEND ESCALATION WHATSAPP JOB — MULTI-CATEGORY
 *
 * Sends a WhatsApp message to a user via the configured WhatsApp API.
 * Works for ALL 4 escalation categories — the message content is
 * fully assembled by BaseEscalationService before dispatch.
 *
 * Usage:
 *   SendEscalationWhatsAppJob::dispatch($user, $phoneNumber, $messageText);
 */
class SendEscalationWhatsAppJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 30;

    public function __construct(
        private readonly User   $user,
        private readonly string $phone,
        private readonly string $message,
    ) {}

    public function handle(): void
    {
        // ── Sanitise phone number ────────────────────────────────────────────
        // Strip all non-digit characters, then ensure country code is present.
        $phone = preg_replace('/\D/', '', $this->phone);

        // If number doesn't start with a country code (heuristic: < 11 digits),
        // prepend the default country code from config.
        if (strlen($phone) < 11) {
            $defaultCode = config('whatsapp.default_country_code', '91'); // India default
            $phone = $defaultCode . $phone;
        }

        try {
            $apiUrl   = config('whatsapp.api_url');
            $apiToken = config('whatsapp.api_token');

            if (!$apiUrl || !$apiToken) {
                Log::warning("WhatsApp not configured — skipping escalation WhatsApp for User #{$this->user->id}");
                return;
            }

            $response = Http::withToken($apiToken)
                ->timeout(20)
                ->post($apiUrl, [
                    'phone'   => $phone,
                    'message' => $this->message,
                ]);

            if ($response->successful()) {
                Log::info("Escalation WhatsApp sent to User #{$this->user->id} ({$phone})");
            } else {
                Log::warning(
                    "Escalation WhatsApp API returned non-200 for User #{$this->user->id}",
                    ['status' => $response->status(), 'body' => $response->body()]
                );
            }
        } catch (\Exception $e) {
            Log::error("Escalation WhatsApp FAILED for User #{$this->user->id}: {$e->getMessage()}");
            throw $e;
        }
    }
}
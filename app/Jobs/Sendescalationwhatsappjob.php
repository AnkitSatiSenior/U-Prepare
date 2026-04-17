<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\User;
use App\Models\WhatsAppLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * SEND ESCALATION WHATSAPP JOB
 *
 * Matches the EXACT same API pattern as SendExpiredSecurityWhatsAppJob
 * (x-api-key header, same endpoint, WhatsAppLog logging).
 */
class SendEscalationWhatsAppJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 30;

    public function __construct(
        public readonly User   $user,
        public readonly string $validPhone,
        public readonly string $message,
    ) {}

    public function handle(): void
    {
        $baseUrl = rtrim((string) config('services.whatsapp.url', ''), '/');
        $apiKey  = (string) config('services.whatsapp.key', '');
        $apiUrl  = $baseUrl . '/api/whatsapp/send-text';

        $logData = [
            'user_id'      => $this->user->id,
            'to_number'    => $this->validPhone,
            'message_body' => $this->message,
            'status'       => 'queued',
            'sent_at'      => now(),
        ];

        if (empty($baseUrl) || empty($apiKey)) {
            Log::warning('[EscalationWhatsApp] WhatsApp not configured — skipping.', [
                'user_id' => $this->user->id,
            ]);
            return;
        }

        try {
            $response = Http::withHeaders([
                    'x-api-key'    => $apiKey,      // ← exact same as existing jobs
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->timeout(30)
                ->retry(2, 1000)
                ->post($apiUrl, [
                    'number'  => $this->validPhone,
                    'message' => $this->message,
                ]);

            $response->throw();

            if (!$response->json('success')) {
                throw new Exception('WhatsApp API returned success:false — ' . $response->json('error', 'Unknown'));
            }

            $logData['status']   = 'sent';
            $logData['response'] = json_encode($response->json());
            WhatsAppLog::create($logData);

        } catch (Exception $e) {
            $logData['status']        = 'failed';
            $logData['error_message'] = $e->getMessage();
            WhatsAppLog::create($logData);

            Log::error('[EscalationWhatsApp] Failed: ' . $e->getMessage(), [
                'user_id' => $this->user->id,
                'phone'   => $this->validPhone,
            ]);

            throw $e; // Re-throw so queue retries
        }
    }

    /**
     * Sanitise and format phone number to Indian format (91XXXXXXXXXX).
     * Call this BEFORE dispatching the job.
     */
    public static function formatPhone(?string $phone): ?string
    {
        if (empty($phone)) return null;

        $clean = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($clean) === 10)                                    return '91' . $clean;
        if (strlen($clean) === 12 && str_starts_with($clean, '91')) return $clean;
        if (strlen($clean) === 11 && str_starts_with($clean, '0'))  return '91' . substr($clean, 1);

        return null;
    }
}
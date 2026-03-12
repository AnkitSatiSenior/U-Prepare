<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\ContractSecurity;
use App\Models\WhatsAppLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;
use Exception;

class SendExpiredSecurityWhatsAppJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     * Note: WhatsApp APIs can rate-limit heavily. 3 retries is standard.
     */
    public int $tries = 3;

    public function __construct(
        public ContractSecurity $security,
        public User $user,
        public string $message
    ) {}

    public function handle(): void
    {
        $apiUrl = config('services.whatsapp.url') . '/whatsapp/send-text';
        $apiKey = config('services.whatsapp.key');
        
        $logData = [
            'security_id' => $this->security->id,
            'to_number'   => $this->user->phone,
            'message'     => $this->message,
            'status'      => 'queued',
            'sent_at'     => now(),
        ];

        try {
            // 1. Send the HTTP Request to your Node.js Microservice
            $response = Http::withToken($apiKey) // Assumes Bearer token authentication in your Node API
                ->timeout(15) // CRITICAL: Never let an external API hang your workers
                ->retry(2, 1000) // Quick inner-retry for temporary network blips
                ->post($apiUrl, [
                    'number'  => $this->user->phone,
                    'message' => $this->message,
                ]);

            // 2. Handle HTTP-level failures (4xx, 5xx)
            $response->throw();

            // 3. Handle Application-level failures (if your Node API returns success: false)
            if (!$response->json('success')) {
                throw new Exception("WhatsApp API returned success: false. Error: " . $response->json('error', 'Unknown'));
            }

            // 4. Log Success
            $logData['status'] = 'sent';
            $logData['response'] = $response->json();
            WhatsAppLog::create($logData); // Assuming a simple create method here

        } catch (Exception $e) {
            // 5. Log Failure
            $logData['status'] = 'failed';
            $logData['error_message'] = $e->getMessage();
            WhatsAppLog::create($logData);
            
            Log::error("[WhatsApp Integration Failed]: " . $e->getMessage(), [
                'user_id' => $this->user->id,
                'phone'   => $this->user->phone
            ]);

            // 6. Re-throw to trigger Laravel's Queue Retry mechanism
            throw $e; 
        }
    }
}
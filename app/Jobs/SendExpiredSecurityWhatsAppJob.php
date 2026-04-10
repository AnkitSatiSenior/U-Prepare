<?php

declare(strict_types=1);

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
use Exception;

class SendExpiredSecurityWhatsAppJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    /**
     * ✅ BUG FIX: Added $validPhone to the constructor to match the Action.
     */
    public function __construct(
        public ContractSecurity $security,
        public User $user,
        public string $validPhone, 
        public string $message
    ) {}

    public function handle(): void
    {
        // ✅ BUG FIX: Use the URL exactly as it is in your .env
        $apiUrl = (string) config('services.whatsapp.url', '');
        $apiKey = (string) config('services.whatsapp.key', '');
        
        $logData = [
            'security_id' => $this->security->id,
            'to_number'   => $this->validPhone, // ✅ Use the sanitized phone number
            'message'     => $this->message,
            'status'      => 'queued',
        ];

        try {
            // ✅ BUG FIX: Use x-api-key header instead of Bearer token
            $response = Http::withHeaders([
                    'x-api-key'    => $apiKey,
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->timeout(15) 
                ->retry(2, 1000) 
                ->post($apiUrl, [
                    'number'  => $this->validPhone, // ✅ Use the sanitized phone number
                    'message' => $this->message,
                ]);

            $response->throw();

            if (!$response->json('success')) {
                throw new Exception("WhatsApp API returned success: false. Error: " . $response->json('error', 'Unknown'));
            }

            // Log Success
            $logData['status'] = 'sent';
            $logData['response'] = json_encode($response->json());
            WhatsAppLog::create($logData); 

        } catch (Exception $e) {
            // Log Failure
            $logData['status'] = 'failed';
            $logData['error_message'] = $e->getMessage();
            WhatsAppLog::create($logData);
            
            Log::error("[WhatsApp Integration Failed]: " . $e->getMessage(), [
                'user_id' => $this->user->id,
                'phone'   => $this->validPhone
            ]);

            throw $e; 
        }
    }
}
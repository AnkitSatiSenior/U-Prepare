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

    public function __construct(
        public ContractSecurity $security,
        public User $user,
        public string $validPhone, 
        public string $message
    ) {}

    public function handle(): void
    {
        // 1. Endpoint Configuration (Matched exactly to TestWhatsAppConnection)
        $baseUrl = rtrim((string) config('services.whatsapp.url', ''), '/');
        $apiKey  = (string) config('services.whatsapp.key', '');
        
        $apiUrl  = $baseUrl . '/api/whatsapp/send-text';
        
        $logData = [
            'security_id' => $this->security->id,
            'to_number'   => $this->validPhone,
            'message'     => $this->message,
            'status'      => 'queued',
        ];

        try {
            // 2. HTTP Request (Matched Headers to TestWhatsAppConnection)
            $response = Http::withHeaders([
                    'x-api-key'    => $apiKey,
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->timeout(30) // Matched timeout to command
                ->retry(2, 1000) 
                ->post($apiUrl, [
                    'number'  => $this->validPhone,
                    'message' => $this->message,
                ]);

            $response->throw();

            if (!$response->json('success')) {
                throw new Exception("WhatsApp API returned success: false. Error: " . $response->json('error', 'Unknown'));
            }

            // 3. Log Success
            $logData['status'] = 'sent';
            $logData['response'] = json_encode($response->json());
            WhatsAppLog::create($logData); 

        } catch (Exception $e) {
            // 4. Log Failure
            $logData['status'] = 'failed';
            $logData['error_message'] = $e->getMessage();
            WhatsAppLog::create($logData);
            
            Log::error("[WhatsApp Integration Failed]: " . $e->getMessage(), [
                'user_id' => $this->user->id,
                'phone'   => $this->validPhone,
                'url'     => $apiUrl // Added URL to log for easier debugging
            ]);

            throw $e; 
        }
    }
}
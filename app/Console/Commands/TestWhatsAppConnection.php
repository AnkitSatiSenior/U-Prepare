<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;
use Exception;

class TestWhatsAppConnection extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'whatsapp:test 
                            {phone=918868945220 : Target phone number} 
                            {--message=System integration test via Cloudflare. : Default message}
                            {--security : Flag to generate and send a dummy expired security alert}';

    /**
     * The console command description.
     */
    protected $description = 'Pings the Node.js WhatsApp microservice via x-api-key to verify connectivity.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $phone = $this->argument('phone');
        
        // 1. Dynamic Payload Generation
        if ($this->option('security')) {
            $message = $this->buildSecurityPayload();
        } else {
            $message = $this->option('message');
        }
        
        // 2. Endpoint Configuration
        $baseUrl = rtrim((string) config('services.whatsapp.url', ''), '/');
        $apiKey  = (string) config('services.whatsapp.key', '');

        $this->components->info("🚀 Dispatching test message to {$phone}");

        // 3. Pre-flight Validation
        if (empty($apiKey) || empty($baseUrl)) {
            $this->components->error("❌ Missing configuration. Check WHATSAPP_API_URL and WHATSAPP_API_KEY in .env.");
            return self::FAILURE;
        }

        $apiUrl = $baseUrl . '/api/whatsapp/send-text';

        try {
            // 4. HTTP Request
            $response = Http::withHeaders([
                    'x-api-key'    => $apiKey,
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->timeout(30) 
                ->post($apiUrl, [
                    'number'  => $phone,
                    'message' => $message,
                ]);

            // 5. Handle Success
            if ($response->successful() && $response->json('success')) {
                $this->components->info('✅ Integration Successful!');
                return self::SUCCESS;
            }

            // 6. Handle API Rejections (4xx / 5xx)
            $this->components->error('❌ API rejected the request.');
            $this->table(
                ['Status', 'Error'], 
                [[$response->status(), $response->json('error') ?? $response->body()]]
            );
            
            return self::FAILURE;

        } catch (ConnectionException $e) {
            // 7. Handle Infrastructure/Network Failures
            $this->components->error('🚨 Connection Failed!');
            $this->line('Message: ' . $e->getMessage());
            
            if (str_contains($e->getMessage(), 'resolve host')) {
                $this->components->warn('Tip: Your Cloudflare Tunnel URL might have expired. Check your Ubuntu terminal.');
            }

            return self::FAILURE;
        } catch (Exception $e) {
            // Catch any other unexpected exceptions
            $this->components->error('🚨 Unexpected Error!');
            $this->line('Message: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    /**
     * Build the Markdown template for the security alert.
     */
    private function buildSecurityPayload(): string
    {
        $securityId = random_int(1000, 9999);
        $userName = 'System Admin';
        $projectName = 'U-Prepare Architecture';
        
        return "🚨 *SECURITY EXPIRATION ALERT* 🚨\n\n"
             . "Hello *{$userName}*,\n\n"
             . "This is an automated notification from the *U-Prepare* compliance system. A critical security document has reached its expiration date.\n\n"
             . "📄 *Document Details:*\n"
             . "▪️ *Ref ID:* [SEC-{$securityId}]\n"
             . "▪️ *Project:* {$projectName}\n"
             . "▪️ *Status:* ❌ _EXPIRED_\n\n"
             . "Please log in to the administration portal to renew the clearance immediately.";
    }
}
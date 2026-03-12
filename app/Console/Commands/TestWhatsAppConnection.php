<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

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

    public function handle(): int
    {
        $phone = $this->argument('phone');
        
        // 1. Dynamic Payload Generation (Beautiful WhatsApp Template)
        if ($this->option('security')) {
            $securityId = random_int(1000, 9999);
            $userName = 'System Admin';
            $projectName = 'U-Prepare Architecture';
            
            // Using WhatsApp Markdown: *bold*, _italics_, and newlines (\n)
            $message = "🚨 *SECURITY EXPIRATION ALERT* 🚨\n\n"
                     . "Hello *{$userName}*,\n\n"
                     . "This is an automated notification from the *U-Prepare* compliance system. A critical security document has reached its expiration date.\n\n"
                     . "📄 *Document Details:*\n"
                     . "▪️ *Ref ID:* [SEC-{$securityId}]\n"
                     . "▪️ *Project:* {$projectName}\n"
                     . "▪️ *Status:* ❌ _EXPIRED_\n\n"
                     . "Please log in to the administration portal to renew the clearance immediately.";
                     
        } else {
            $message = $this->option('message');
        }
        
        // 2. Endpoint Configuration
        $baseUrl = rtrim(config('services.whatsapp.url', ''), '/');
        $apiUrl = $baseUrl . '/api/whatsapp/send-text';
        $apiKey = config('services.whatsapp.key', '');

        $this->components->info("🚀 Dispatching test message to {$phone}");
        $this->line("🔗 Endpoint: {$apiUrl}");
        $this->line("<fg=gray>Payload:</>");
        $this->line("<fg=cyan>{$message}</>"); // Output the template to the console too

        // 3. Pre-flight Validation
        if (empty($apiKey) || empty($baseUrl)) {
            $this->components->error("❌ Missing configuration. Check WHATSAPP_API_URL and WHATSAPP_API_KEY in .env.");
            return self::FAILURE;
        }

        try {
            // 4. HTTP Request (Timeout accounts for Tunnel latency)
            $response = Http::withHeaders([
                    'x-api-key' => $apiKey,
                    'Accept'    => 'application/json',
                    'Content-Type'=> 'application/json',
                ])
                ->timeout(30) 
                ->post($apiUrl, [
                    'number'  => $phone,
                    'message' => $message,
                ]);

            // 5. Handle Success
            if ($response->successful() && $response->json('success')) {
                $this->components->info('✅ Integration Successful!');
                $this->line('Node API Response: ' . json_encode($response->json(), JSON_PRETTY_PRINT));
                return self::SUCCESS;
            }

            // 6. Handle Logic Failures (Validation or Auth)
            $this->components->error('❌ API rejected the request.');
            $this->table(['Status', 'Error'], [
                [$response->status(), $response->json('error') ?? $response->body()]
            ]);
            
            return self::FAILURE;

        } catch (\Exception $e) {
            // 7. Handle Infrastructure/Network Failures
            $this->components->error('🚨 Connection Failed!');
            $this->line('Message: ' . $e->getMessage());
            
            if (str_contains($e->getMessage(), 'Couldn\'t resolve host')) {
                $this->components->warn('Tip: Your Cloudflare Tunnel URL might have expired. Check your Ubuntu terminal.');
            }

            return self::FAILURE;
        }
    }
}
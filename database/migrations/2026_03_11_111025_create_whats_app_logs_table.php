<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('whatsapp_logs', function (Blueprint $table) {
            $table->id();
            
            // Relational tracking
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('security_id')->nullable()->index(); // Optional: Link to the specific contract/security
            
            // Message payload
            $table->string('to_number');
            $table->text('message_body');
            
            // Status and debugging
            $table->string('status')->default('queued')->index(); // e.g., 'queued', 'sent', 'failed'
            $table->json('response')->nullable(); // Store the raw Node API response
            $table->text('error_message')->nullable(); // Store Laravel/Guzzle exception messages
            
            // Timestamps
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Optional: keep history even if "deleted"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_logs');
    }
};
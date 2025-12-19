<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            // User who did the action (nullable for system actions)
            $table->unsignedBigInteger('user_id')->nullable();

            // Model info
            $table->string('model_type'); // e.g., App\Models\SocialSafeguardEntry
            $table->unsignedBigInteger('model_id')->nullable(); // e.g., 45

            // What action was performed
            $table->string('action'); // created, updated, deleted, restored

            // JSON of changes { old: {}, new: {} }
            $table->json('changes')->nullable();

            // Request metadata
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('url')->nullable();

            $table->timestamps();

            // Foreign key relation (optional)
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null'); // If user deleted, keep log
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};

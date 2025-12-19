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
        Schema::create('media_files', function (Blueprint $table) {
            $table->id();

            // File path (relative or absolute)
            $table->string('path');

            // Type of file: image, document, pdf, etc.
            $table->string('type')->index();

            // Store metadata in JSON format (width, height, size, mime_type, etc.)
            $table->json('meta_data')->nullable();

            // Timestamps for created/updated
            $table->timestamps();

            // Soft delete for safe removal
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_files');
    }
};

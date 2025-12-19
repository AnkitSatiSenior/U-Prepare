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
        Schema::create('physical_epc_progress', function (Blueprint $table) {
            $table->id();
            
            // Linking to epcentry_data table
            $table->unsignedBigInteger('epcentry_data_id');
            $table->foreign('epcentry_data_id')
                  ->references('id')
                  ->on('epcentry_data')
                  ->onDelete('cascade');

            // Progress fields
            $table->decimal('percent', 5, 2)->nullable();
            $table->decimal('amount', 15, 2)->nullable();

            // Additional fields
            $table->text('items')->nullable();
            $table->date('progress_submitted_date')->nullable();
            $table->json('images')->nullable(); // Store multiple images as JSON array

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('physical_epc_progress');
    }
};

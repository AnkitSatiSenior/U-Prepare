<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('boqentry_variations', function (Blueprint $table) {
            $table->id();

            // Link with main BOQ table
            $table->unsignedBigInteger('boqentry_id');

            // Old and new values
            $table->decimal('old_qty', 12, 3)->nullable();
            $table->decimal('new_qty', 12, 3)->nullable();

            $table->decimal('old_rate', 12, 2)->nullable();
            $table->decimal('new_rate', 12, 2)->nullable();

            $table->decimal('old_amount', 15, 2)->nullable();
            $table->decimal('new_amount', 15, 2)->nullable();

            // What changed
            $table->enum('changed_field', ['qty', 'rate', 'both', 'amount'])->default('both');

            // Optional notes
            $table->text('remarks')->nullable();

            $table->timestamps();

            // Foreign key
            $table->foreign('boqentry_id')->references('id')->on('boqentry_data')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boqentry_variations');
    }
};

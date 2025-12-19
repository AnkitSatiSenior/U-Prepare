<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('physical_boq_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('boq_entry_id')->constrained('boqentry_data')->onDelete('cascade');
            $table->decimal('qty', 15, 2);
            $table->decimal('amount', 15, 2);
            $table->date('progress_submitted_date')->nullable();
            $table->json('media')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('long', 10, 7)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('physical_boq_progress');
    }
};

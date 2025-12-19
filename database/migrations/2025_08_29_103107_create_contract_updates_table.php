<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contract_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('contracts')->onDelete('cascade');

            $table->decimal('old_contract_value', 15, 2)->nullable();
            $table->decimal('new_contract_value', 15, 2)->nullable();

            $table->date('old_initial_completion_date')->nullable();
            $table->date('new_initial_completion_date')->nullable();

            $table->date('old_actual_completion_date')->nullable();
            $table->date('new_actual_completion_date')->nullable();

            $table->timestamp('changed_at')->useCurrent();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_updates');
    }
};

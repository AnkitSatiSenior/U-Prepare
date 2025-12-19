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
        Schema::create('contract_securities', function (Blueprint $table) {
            $table->id();

            // Relation to contracts table
            $table->foreignId('contract_id')
                  ->constrained('contracts')
                  ->onDelete('cascade');

            // Foreign keys to types & forms
            $table->foreignId('security_type_id')
                  ->constrained('contract_security_types')
                  ->onDelete('cascade');

            $table->foreignId('security_form_id')
                  ->constrained('contract_security_forms')
                  ->onDelete('cascade');

            // Security details
            $table->date('issue_date')->nullable();
            $table->date('issued_end_date')->nullable();
            $table->string('security_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->decimal('value', 15, 2)->nullable();
            $table->string('file_path')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_securities');
    }
};

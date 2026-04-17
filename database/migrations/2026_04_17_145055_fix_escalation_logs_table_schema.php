<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop the broken table
        Schema::dropIfExists('escalation_logs');

        // 2. Recreate the perfect table with compliance_id
        Schema::create('escalation_logs', function (Blueprint $table) {
            $table->id();
            $table->morphs('escalatable'); 
            
            // This is the missing column causing your error!
            $table->unsignedBigInteger('compliance_id');
            
            $table->unsignedInteger('day_mark'); 
            $table->unsignedInteger('level');    
            $table->string('type', 50);          
            $table->timestamps();

            // System Integrity Constraint
            $table->unique(
                ['escalatable_id', 'escalatable_type', 'compliance_id', 'day_mark', 'level'], 
                'unique_escalation_guard'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('escalation_logs');
    }
};
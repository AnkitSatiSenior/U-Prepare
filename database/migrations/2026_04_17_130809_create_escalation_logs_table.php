<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_create_escalation_logs_table.php
public function up()
{
    Schema::create('escalation_logs', function (Blueprint $table) {
        $table->id();
        $table->morphs('escalatable'); // Can attach to a Shift, Form, Task, etc.
        $table->integer('day_mark');   // 1, 7, 14, 17, etc.
        $table->integer('level');      // 1, 2, 3...
        $table->string('type');        // alert or reminder
        $table->timestamps();
        
        // Prevent duplicate sends for the same item, day, and level
        $table->unique(['escalatable_id', 'escalatable_type', 'day_mark', 'level'], 'unique_escalation_log');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escalation_logs');
    }
};

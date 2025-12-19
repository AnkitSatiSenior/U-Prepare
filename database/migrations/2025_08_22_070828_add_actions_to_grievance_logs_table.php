<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grievance_logs', function (Blueprint $table) {
            $table->text('preliminary_action_taken')->nullable()->after('remark');
            $table->text('final_action_taken')->nullable()->after('preliminary_action_taken');
        });
    }

    public function down(): void
    {
        Schema::table('grievance_logs', function (Blueprint $table) {
            $table->dropColumn(['preliminary_action_taken', 'final_action_taken']);
        });
    }
};

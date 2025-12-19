<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grievance_logs', function (Blueprint $table) {
            // Drop old columns
            if (Schema::hasColumn('grievance_logs', 'preliminary_action_taken')) {
                $table->dropColumn('preliminary_action_taken');
            }
            if (Schema::hasColumn('grievance_logs', 'final_action_taken')) {
                $table->dropColumn('final_action_taken');
            }

            // Make sure `type` exists
            if (!Schema::hasColumn('grievance_logs', 'type')) {
                $table->enum('type', ['preliminary', 'final', 'log'])->default('log')->after('user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('grievance_logs', function (Blueprint $table) {
            // Re-add old columns if rolling back
            $table->text('preliminary_action_taken')->nullable();
            $table->text('final_action_taken')->nullable();

            // Optional: remove type column if added by this migration
            if (Schema::hasColumn('grievance_logs', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};

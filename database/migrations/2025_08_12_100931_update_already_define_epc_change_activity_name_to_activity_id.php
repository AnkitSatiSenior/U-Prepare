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
        Schema::table('already_define_epc', function (Blueprint $table) {
            // Drop activity_name if it exists
            if (Schema::hasColumn('already_define_epc', 'activity_name')) {
                $table->dropColumn('activity_name');
            }

            // Add activity_id if it doesn't exist
            if (!Schema::hasColumn('already_define_epc', 'activity_id')) {
                $table->unsignedBigInteger('activity_id')->nullable()->after('sl_no');
                $table->foreign('activity_id')
                    ->references('id')
                    ->on('epc_activity_names')
                    ->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('already_define_epc', function (Blueprint $table) {
            if (Schema::hasColumn('already_define_epc', 'activity_id')) {
                $table->dropForeign(['activity_id']);
                $table->dropColumn('activity_id');
            }

            if (!Schema::hasColumn('already_define_epc', 'activity_name')) {
                $table->string('activity_name')->after('sl_no');
            }
        });
    }
};

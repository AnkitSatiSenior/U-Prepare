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
        Schema::table('safeguard_entries', function (Blueprint $table) {
            // Add new column order_by with default 0
            $table->integer('order_by')
                  ->default(0)
                  ->after('is_major_head'); // positioning
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('safeguard_entries', function (Blueprint $table) {
            $table->dropColumn('order_by');
        });
    }
};

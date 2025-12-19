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
        Schema::table('already_define_safeguard_entries', function (Blueprint $table) {
            $table->boolean('is_parent')->default(0)->after('is_major_head');
        });

        Schema::table('safeguard_entries', function (Blueprint $table) {
            $table->boolean('is_parent')->default(0)->after('is_major_head');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('already_define_safeguard_entries', function (Blueprint $table) {
            $table->dropColumn('is_parent');
        });

        Schema::table('safeguard_entries', function (Blueprint $table) {
            $table->dropColumn('is_parent');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('safeguard_entries', function (Blueprint $table) {
            $table->boolean('is_major_head')->nullable()->default(0)->after('is_validity');
        });
    }

    public function down(): void
    {
        Schema::table('safeguard_entries', function (Blueprint $table) {
            $table->dropColumn('is_major_head');
        });
    }
};

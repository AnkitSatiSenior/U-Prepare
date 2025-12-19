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
        Schema::table('package_components', function (Blueprint $table) {
            $table->text('description')->nullable()->after('budget'); // ✅ new column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('package_components', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};

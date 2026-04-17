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
        Schema::table('roles', function (Blueprint $table) {
            // 1. Add 'level' as unsigned integer
            // 2. Set default to 0 to prevent NULL issues in existing rows
            // 3. Add index as this column is likely used for ordering/authorization logic
            $table->unsignedInteger('level')->default(0)->after('name')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            // Always drop the index before the column for clean rollbacks
            $table->dropIndex(['level']);
            $table->dropColumn('level');
        });
    }
};
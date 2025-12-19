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
        Schema::table('contraction_phases', function (Blueprint $table) {
            $table->boolean('is_one_time')->default(false)->after('name'); // Add column after 'name'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contraction_phases', function (Blueprint $table) {
            $table->dropColumn('is_one_time'); // Remove column on rollback
        });
    }
};

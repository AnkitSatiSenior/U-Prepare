<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('safeguard_compliances', function (Blueprint $table) {
            // Using JSON column to store multiple contraction_phase IDs
            $table->json('contraction_phase_ids')->nullable()->after('role_id');
        });
    }

    public function down(): void
    {
        Schema::table('safeguard_compliances', function (Blueprint $table) {
            $table->dropColumn('contraction_phase_ids');
        });
    }
};

<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('safeguard_entries', function (Blueprint $table) {
            $table->boolean('is_validity')->default(0)->after('deleted_at'); // Add column after deleted_at
        });
    }

    public function down(): void
    {
        Schema::table('safeguard_entries', function (Blueprint $table) {
            $table->dropColumn('is_validity'); // Rollback
        });
    }
};

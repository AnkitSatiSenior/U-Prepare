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
        Schema::table('safeguard_compliances', function (Blueprint $table) {
            if (!Schema::hasColumn('safeguard_compliances', 'role_id')) {
                $table->foreignId('role_id')
                      ->nullable()
                      ->constrained('roles')
                      ->onDelete('cascade')
                      ->after('id'); // optional: puts it after id
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('safeguard_compliances', function (Blueprint $table) {
            if (Schema::hasColumn('safeguard_compliances', 'role_id')) {
                $table->dropForeign(['role_id']); // drop FK constraint
                $table->dropColumn('role_id');    // drop column
            }
        });
    }
};

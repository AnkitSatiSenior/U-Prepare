<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('package_projects', 'safeguard_exists')) {
            Schema::table('package_projects', function (Blueprint $table) {
                $table->boolean('safeguard_exists')
                      ->default(true)
                      ->after('status')
                      ->comment('Indicates if safeguards exist for this project');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('package_projects', 'safeguard_exists')) {
            Schema::table('package_projects', function (Blueprint $table) {
                $table->dropColumn('safeguard_exists');
            });
        }
    }
};

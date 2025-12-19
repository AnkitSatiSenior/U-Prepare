<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('package_projects', function (Blueprint $table) {
            if (!Schema::hasColumn('package_projects', 'status')) {
                $table->string('status')->nullable()->after('deleted_at');
                // Optional default: ->default('pending')
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('package_projects', function (Blueprint $table) {
            if (Schema::hasColumn('package_projects', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};

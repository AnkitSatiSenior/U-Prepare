<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->index('funding_agency_id');
            $table->index('is_dli_based');
            $table->index('is_revised');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex(['funding_agency_id']);
            $table->dropIndex(['is_dli_based']);
            $table->dropIndex(['is_revised']);
            $table->dropIndex(['deleted_at']);
        });
    }
};


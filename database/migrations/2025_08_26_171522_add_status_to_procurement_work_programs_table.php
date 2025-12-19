<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('procurement_work_programs', function (Blueprint $table) {
            if (!Schema::hasColumn('procurement_work_programs', 'status')) {
                $table->tinyInteger('status')->default(0)->after('updated_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('procurement_work_programs', function (Blueprint $table) {
            if (Schema::hasColumn('procurement_work_programs', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};

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
        Schema::table('package_projects', function (Blueprint $table) {
            $table->unsignedBigInteger('sub_department_id')
                ->nullable()
                ->after('department_id');

            $table->foreign('sub_department_id')
                ->references('id')
                ->on('sub_departments')
                ->nullOnDelete(); // if sub-department deleted, set NULL
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('package_projects', function (Blueprint $table) {
            $table->dropForeign(['sub_department_id']);
            $table->dropColumn('sub_department_id');
        });
    }
};

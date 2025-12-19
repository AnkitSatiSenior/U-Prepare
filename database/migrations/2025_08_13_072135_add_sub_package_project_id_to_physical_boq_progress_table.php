<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('physical_boq_progress', function (Blueprint $table) {
            $table->unsignedBigInteger('sub_package_project_id')->after('id')->nullable()->index();

            // Optional: add foreign key if you want referential integrity
            $table->foreign('sub_package_project_id')
                ->references('id')
                ->on('sub_package_projects')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('physical_boq_progress', function (Blueprint $table) {
            $table->dropForeign(['sub_package_project_id']);
            $table->dropColumn('sub_package_project_id');
        });
    }
};

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
            $table->unsignedBigInteger('package_component_id')->nullable()->after('id');

            // optional: foreign key relation
            $table->foreign('package_component_id')
                  ->references('id')
                  ->on('package_components')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('package_projects', function (Blueprint $table) {
            $table->dropForeign(['package_component_id']);
            $table->dropColumn('package_component_id');
        });
    }
};

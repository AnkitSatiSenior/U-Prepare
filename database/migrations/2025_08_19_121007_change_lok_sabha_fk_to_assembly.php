<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('package_projects', function (Blueprint $table) {
            // Drop the old foreign key
            $table->dropForeign(['lok_sabha_id']); 

            // Add new foreign key pointing to assembly
            $table->foreign('lok_sabha_id')
                  ->references('id')
                  ->on('assembly')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('package_projects', function (Blueprint $table) {
            // Drop the assembly foreign key
            $table->dropForeign(['lok_sabha_id']);

            // Restore old foreign key to constituencies
            $table->foreign('lok_sabha_id')
                  ->references('id')
                  ->on('constituencies')
                  ->onDelete('set null');
        });
    }
};

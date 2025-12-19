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
        Schema::table('contracts', function (Blueprint $table) {
            // Adding count_sub_project column
            $table->unsignedInteger('count_sub_project')
                  ->default(0)
                  ->after('contractor_id')
                  ->index(); // Index for faster filtering
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropIndex(['count_sub_project']); // drop index first
            $table->dropColumn('count_sub_project');
        });
    }
};

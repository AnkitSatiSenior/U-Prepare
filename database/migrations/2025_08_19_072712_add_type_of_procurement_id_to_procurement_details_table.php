<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('procurement_details', function (Blueprint $table) {
            // Add the foreign key column with default 1
            $table->foreignId('type_of_procurement_id')
                  ->default(1)
                  ->constrained('type_of_procurements')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('procurement_details', function (Blueprint $table) {
            $table->dropForeign(['type_of_procurement_id']);
            $table->dropColumn('type_of_procurement_id');
        });
    }
};

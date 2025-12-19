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
        // database/migrations/YYYY_MM_DD_create_geography_blocks_table.php
Schema::create('geography_blocks', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('division_id');
    $table->unsignedBigInteger('district_id');
    $table->string('name');
    $table->string('slug')->unique();
    $table->timestamps();
    $table->softDeletes();

    $table->foreign('division_id')
          ->references('id')
          ->on('geography_divisions')
          ->onDelete('cascade');

    $table->foreign('district_id')
          ->references('id')
          ->on('geography_districts')
          ->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geography_blocks');
    }
};

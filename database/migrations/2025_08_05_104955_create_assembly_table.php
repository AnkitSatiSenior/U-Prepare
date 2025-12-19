<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('assembly', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('district_id');
            $table->unsignedBigInteger('constituency_id');
            $table->string('name');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('district_id')
                  ->references('id')
                  ->on('geography_districts')
                  ->onDelete('cascade');

            // If you have a constituencies table, add this:
            $table->foreign('constituency_id')
                  ->references('id')
                  ->on('constituencies')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('assembly');
    }
};
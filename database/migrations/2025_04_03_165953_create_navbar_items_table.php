<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('navbar_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->boolean('is_dropdown')->default(false);
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('route')->nullable();
            $table->string('url')->nullable();
            $table->string('icon')->nullable();
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('navbar_items')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('navbar_items');
    }
};
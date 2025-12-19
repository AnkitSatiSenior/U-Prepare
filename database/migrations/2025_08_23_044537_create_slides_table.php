<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slides', function (Blueprint $table) {
            $table->id();
            $table->string('img'); // store image path
            $table->string('head')->nullable(); // heading
            $table->string('subh')->nullable(); // subheading
            $table->string('btn_text')->nullable(); // button text
            $table->string('link')->nullable(); // button link
            $table->integer('order')->default(0); // for ordering slides
            $table->boolean('status')->default(true); // active/inactive
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slides');
    }
};

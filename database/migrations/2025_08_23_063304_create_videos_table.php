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
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('img'); // thumbnail image path
            $table->string('link'); // video link (YouTube/Vimeo/etc.)
            $table->string('text')->nullable(); // caption or title
            $table->boolean('status')->default(true); // active/inactive
            $table->integer('order')->default(0); // ordering
            $table->timestamps();
            $table->softDeletes(); // in case you want trash functionality
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};

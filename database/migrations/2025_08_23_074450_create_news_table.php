<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title_en', 255);
            $table->string('title_hi', 255);
            $table->longText('body_en')->nullable();
            $table->longText('body_hi')->nullable();
            $table->string('file')->nullable(); // store image/file path
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->softDeletes(); // ✅ adds deleted_at column
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};

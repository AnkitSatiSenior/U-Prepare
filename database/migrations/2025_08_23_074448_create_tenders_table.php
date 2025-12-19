<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tenders', function (Blueprint $table) {
            $table->id();
            $table->string('title_en', 255);
            $table->string('title_hi', 255);
            $table->text('description_en')->nullable();
            $table->text('description_hi')->nullable();
            $table->string('file')->nullable(); // store file path
            $table->date('open_date');
            $table->date('close_date');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->softDeletes(); // ✅ adds deleted_at column
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenders');
    }
};

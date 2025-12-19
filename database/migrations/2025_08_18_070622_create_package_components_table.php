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
        Schema::create('package_components', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('budget', 15, 2)->nullable(); // budget with 2 decimal places
            $table->timestamps();  // adds created_at & updated_at
            $table->softDeletes(); // adds deleted_at column for soft delete
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_components');
    }
};

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
        Schema::create('user_safeguard_subpackage', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('safeguard_compliance_id')->constrained()->onDelete('cascade');
            $table->foreignId('sub_package_project_id')->constrained()->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();

            // Prevent duplicates
            $table->unique(['user_id', 'safeguard_compliance_id', 'sub_package_project_id'], 'unique_assignment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_safeguard_subpackage');
    }
};

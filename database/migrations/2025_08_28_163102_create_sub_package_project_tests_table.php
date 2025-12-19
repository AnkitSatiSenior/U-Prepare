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
        Schema::create('sub_package_project_tests', function (Blueprint $table) {
            $table->id();

            // Reference to SubPackageProject
            $table->foreignId('sub_package_project_id')
                ->constrained('sub_package_projects')
                ->onDelete('cascade');

            // Reference to Test Type
            $table->foreignId('test_type_id')
                ->constrained('sub_package_project_test_types')
                ->onDelete('cascade');

            // Store the name/type of test for quick access (optional)
            $table->string('test_name');
            $table->string('test_type');

            $table->timestamps();
            $table->softDeletes(); // Soft delete support
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_package_project_tests');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sub_package_project_test_reports', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('test_id')->constrained('sub_package_project_tests')->onDelete('cascade');

            $table->text('report')->nullable();
            $table->string('file')->nullable();
            $table->text('remark')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');

            $table->timestamps(); // created_at & updated_at
            $table->softDeletes(); // soft delete support
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_package_project_test_reports');
    }
};

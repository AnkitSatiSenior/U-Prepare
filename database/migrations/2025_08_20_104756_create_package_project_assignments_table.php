<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_project_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('package_project_id')
                  ->constrained('package_projects')
                  ->onDelete('cascade');

            $table->foreignId('assigned_to')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->foreignId('assigned_by')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_project_assignments');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grievance_assignments', function (Blueprint $table) {
            $table->id();

            // FK to grievances
            $table->unsignedBigInteger('grievance_id');
            $table->foreign('grievance_id')
                  ->references('id')
                  ->on('grievances')
                  ->onDelete('cascade');

            // Assigned to user
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->foreign('assigned_to')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');

            // Assigned by user (admin/officer)
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->foreign('assigned_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');

            // Optional department/district reference
            $table->string('department')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grievance_assignments');
    }
};

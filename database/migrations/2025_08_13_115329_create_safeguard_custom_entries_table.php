<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_safeguard_entries', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->unsignedBigInteger('sub_package_project_id')->nullable();
            $table->unsignedBigInteger('social_compliance_id')->nullable();
            $table->unsignedBigInteger('contraction_phase_id')->nullable();

            // Entry-specific fields
            $table->tinyInteger('yes_no')->nullable(); // 0, 1, or 2
            $table->json('photos_documents_case_studies')->nullable();
            $table->text('remarks')->nullable();
            $table->date('validity_date')->nullable();
            $table->date('date_of_entry')->nullable();

            // Timestamps & soft delete
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('sub_package_project_id')
                  ->references('id')->on('sub_package_projects')
                  ->onDelete('set null');

            $table->foreign('social_compliance_id')
                  ->references('id')->on('safeguard_compliances')
                  ->onDelete('set null');

            $table->foreign('contraction_phase_id')
                  ->references('id')->on('contraction_phases')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_safeguard_entries');
    }
};

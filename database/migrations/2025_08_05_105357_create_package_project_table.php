<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_projects', function (Blueprint $table) {
            $table->id();

            // Project and Category References
            $table->foreignId('project_id')
                ->nullable()
                ->constrained('projects')
                ->onDelete('set null');

            $table->foreignId('package_category_id')
                ->nullable()
                ->constrained('projects_category')
                ->onDelete('set null');

            $table->foreignId('package_sub_category_id')
                ->nullable()
                ->constrained('sub_category')
                ->onDelete('set null');

            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->onDelete('set null');

            // Package Details
            $table->text('package_name');
            $table->string('package_number')->unique();
            $table->decimal('estimated_budget_incl_gst', 15, 2);

            // Location References
            $table->foreignId('vidhan_sabha_id')
                ->nullable()
                ->constrained('constituencies')
                ->onDelete('set null');

            $table->foreignId('lok_sabha_id')
                ->nullable()
                ->constrained('constituencies')
                ->onDelete('set null');

            $table->foreignId('district_id')
                ->nullable()
                ->constrained('geography_districts')
                ->onDelete('set null');

            $table->foreignId('block_id')
                ->nullable()
                ->constrained('geography_blocks')
                ->onDelete('set null');

            // Approval Sections
            $table->boolean('dec_approved')->default(false);
            $table->date('dec_approval_date')->nullable();
            $table->string('dec_letter_number')->nullable();
            $table->string('dec_document_path')->nullable();

            $table->boolean('hpc_approved')->default(false);
            $table->date('hpc_approval_date')->nullable();
            $table->string('hpc_letter_number')->nullable();
            $table->string('hpc_document_path')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_projects');
    }
};
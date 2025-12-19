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
        // Table storing actual work progress for projects
        Schema::create('work_progress_data', function (Blueprint $table) {
            $table->id(); // Primary key

            // Link to project
            $table->unsignedBigInteger('project_id')->comment('Reference to sub_package_projects table');
            $table->foreign('project_id')
                  ->references('id')
                  ->on('sub_package_projects')
                  ->onDelete('cascade');

            // Link to already defined work component
            $table->unsignedBigInteger('work_component_id')->comment('Reference to already_defined_work_progress table');
            $table->foreign('work_component_id')
                  ->references('id')
                  ->on('already_defined_work_progress')
                  ->onDelete('cascade');

            $table->string('qty_length')->nullable()->comment('Quantity or length completed');
            $table->string('current_stage')->nullable()->comment('Current stage of the work');
            $table->string('progress_percentage')->nullable()->comment('Progress percentage');
            $table->text('remarks')->nullable()->comment('Remarks for the work component');

            $table->timestamps(); // created_at & updated_at
            $table->softDeletes()->comment('Soft delete timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_progress_data');
    }
};

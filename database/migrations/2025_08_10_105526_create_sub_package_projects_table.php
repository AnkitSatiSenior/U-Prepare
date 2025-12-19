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
        Schema::create('sub_package_projects', function (Blueprint $table) {
            $table->id();
            
            // Foreign key to package_projects
            $table->unsignedBigInteger('project_id');

            // Sub-package details
            $table->string('name', 255)->index(); // index for faster search by name
            $table->decimal('contract_value', 15, 2)->default(0)->index(); // indexed for numeric filtering/sorting

            // Relationships & indexing
            $table->foreign('project_id')
                  ->references('id')
                  ->on('package_projects')
                  ->onDelete('cascade');

            // Timestamps & soft deletes
            $table->timestamps();
            $table->softDeletes();

            // Composite index for common query patterns
            $table->index(['project_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_package_projects');
    }
};

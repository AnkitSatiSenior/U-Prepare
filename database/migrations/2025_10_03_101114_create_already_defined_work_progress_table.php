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
        // Table storing pre-defined work components for work services
        Schema::create('already_defined_work_progress', function (Blueprint $table) {
            $table->id(); // Primary key

            // Foreign key to work_service
            $table->unsignedBigInteger('work_service_id')->comment('Reference to work_service table');
            $table->foreign('work_service_id')->references('id')->on('work_service')->onDelete('cascade');

            $table->string('work_component')->comment('Work Component Name');
            $table->string('type_details')->nullable()->comment('Type or details of the work component');
            $table->string('side_location')->nullable()->comment('Side or location for the work component');

            $table->timestamps(); // created_at & updated_at
            $table->softDeletes()->comment('Soft delete timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('already_defined_work_progress');
    }
};

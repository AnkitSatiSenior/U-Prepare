<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcurementWorkProgramsTable extends Migration
{
    public function up(): void
    {
        Schema::create('procurement_work_programs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('package_project_id');
            $table->foreign('package_project_id')->references('id')->on('package_projects')->onDelete('cascade');

            $table->unsignedBigInteger('procurement_details_id');
            $table->foreign('procurement_details_id')->references('id')->on('procurement_details')->onDelete('cascade');

            $table->string('name_work_program');
            $table->decimal('weightage', 5, 2)->nullable();
            $table->integer('days')->nullable();
            $table->date('start_date')->nullable();
            $table->date('planned_date')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procurement_work_programs');
    }
}

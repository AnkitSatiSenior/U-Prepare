<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractsTable extends Migration
{
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
    $table->id();
    $table->string('contract_number')->unique();
    // Correct foreign key table name here:
    $table->foreignId('project_id')->constrained('package_projects')->onDelete('cascade');
    $table->decimal('contract_value', 15, 2);
    $table->decimal('security', 15, 2)->default(0);
    $table->date('signing_date')->nullable();
    $table->date('commencement_date')->nullable();
    $table->date('initial_completion_date')->nullable();
    $table->date('revised_completion_date')->nullable();
    $table->date('actual_completion_date')->nullable();
    $table->string('contract_document')->nullable();
    $table->foreignId('contractor_id')->constrained('contractors')->onDelete('cascade'); // Assuming contractors table exists
    $table->timestamps();
    $table->softDeletes();
});

    }

    public function down()
    {
        Schema::dropIfExists('contracts');
    }
}

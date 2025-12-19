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
        Schema::create('financial_progress_updates', function (Blueprint $table) {
            $table->id();
            
            // Foreign key to sub_package_projects
            $table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('id')->on('sub_package_projects')->onDelete('cascade');
            
            $table->decimal('finance_amount', 15, 2); // Finance Amount
            $table->integer('no_of_bills'); // Number of Bills
            $table->json('bill_serial_no'); // JSON array of serial numbers
            $table->date('submit_date'); // Submission date
            $table->json('media')->nullable(); // JSON array of uploaded media ids or info
            
            $table->timestamps();
            $table->softDeletes(); // optional soft delete
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_progress_updates');
    }
};

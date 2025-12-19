<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcurementDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('procurement_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_project_id')->constrained()->onDelete('cascade');

            $table->string('method_of_procurement')->nullable();
            $table->string('type_of_procurement')->nullable();
            $table->date('publication_date')->nullable();
            $table->string('publication_document_path')->nullable();
            
            $table->decimal('tender_fee', 12, 2)->nullable();
            $table->decimal('earnest_money_deposit', 12, 2)->nullable();

            $table->integer('bid_validity_days')->nullable();
            $table->integer('emd_validity_days')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('procurement_details');
    }
}

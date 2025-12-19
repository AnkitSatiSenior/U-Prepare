<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEpcentryDataTable extends Migration
{
    public function up()
    {
        Schema::create('epcentry_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sub_package_project_id');
            $table->string('sl_no', 255);
            $table->text('item_description');
            $table->decimal('percent', 5, 2)->nullable(); // example: 99.99
            $table->decimal('amount', 15, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraint
            $table->foreign('sub_package_project_id')
                  ->references('id')
                  ->on('sub_package_projects')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('epcentry_data');
    }
}

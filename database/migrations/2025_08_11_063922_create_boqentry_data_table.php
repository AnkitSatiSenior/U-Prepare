<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoqentryDataTable extends Migration
{
    public function up()
    {
        Schema::create('boqentry_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sub_package_project_id');

            $table->string('sl_no', 1000);
            $table->text('item_description');
            $table->string('unit', 20)->nullable();
            $table->decimal('qty', 12, 3)->nullable();
            $table->decimal('rate', 12, 2)->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraint for sub_package_project_id
            $table->foreign('sub_package_project_id')->references('id')->on('sub_package_projects')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('boqentry_data');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsCategoryTable extends Migration
{
    public function up()
    {
        Schema::create('projects_category', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('methods_of_procurement')->nullable();
            $table->string('status')->default('1');
            $table->timestamps();
            $table->softDeletes(); // Adds deleted_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('projects_category');
    }
}

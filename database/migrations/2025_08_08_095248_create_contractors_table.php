<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractorsTable extends Migration
{
    public function up()
    {
        Schema::create('contractors', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('authorized_personnel_name');
            $table->string('phone', 20)->unique()->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('gst_no')->unique()->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
            $table->softDeletes();  // adds deleted_at column for soft delete
        });
    }

    public function down()
    {
        Schema::dropIfExists('contractors');
    }
}

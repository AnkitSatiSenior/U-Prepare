<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('physical_boq_progress', function (Blueprint $table) {
            $table->json('media')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('physical_boq_progress', function (Blueprint $table) {
            $table->text('media')->change(); // revert back if needed
        });
    }
};

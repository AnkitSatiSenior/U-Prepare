<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddItemDescriptionToEpcentryDataTable extends Migration
{
    public function up()
    {
        Schema::table('epcentry_data', function (Blueprint $table) {
            $table->text('item_description')->nullable()->after('stage_name');
        });
    }

    public function down()
    {
        Schema::table('epcentry_data', function (Blueprint $table) {
            $table->dropColumn('item_description');
        });
    }
}

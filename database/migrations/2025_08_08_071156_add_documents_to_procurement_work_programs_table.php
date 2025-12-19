<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDocumentsToProcurementWorkProgramsTable extends Migration
{
    public function up()
    {
        Schema::table('procurement_work_programs', function (Blueprint $table) {
            $table->string('procurement_bid_document')->nullable()->after('planned_date');
            $table->string('pre_bid_minutes_document')->nullable()->after('procurement_bid_document');
        });
    }

    public function down()
    {
        Schema::table('procurement_work_programs', function (Blueprint $table) {
            $table->dropColumn('procurement_bid_document');
            $table->dropColumn('pre_bid_minutes_document');
        });
    }
}

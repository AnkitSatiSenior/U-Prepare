<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('epcentry_data', function (Blueprint $table) {
            // Rename the existing 'item_description' column to 'activity_name'
            $table->renameColumn('item_description', 'activity_name');

            // Add new 'stage_name' column (nullable string)
            $table->string('stage_name')->nullable()->after('activity_name');
        });
    }

    public function down()
    {
        Schema::table('epcentry_data', function (Blueprint $table) {
            // Revert the 'activity_name' column back to 'item_description'
            $table->renameColumn('activity_name', 'item_description');

            // Drop the 'stage_name' column
            $table->dropColumn('stage_name');
        });
    }
};

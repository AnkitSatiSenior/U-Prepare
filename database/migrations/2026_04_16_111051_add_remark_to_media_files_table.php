<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRemarkToMediaFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('media_files', function (Blueprint $bluePrint) {
            // Adding nullable remark column. 
            // 'after' helps maintain a clean DB schema visual order.
            $bluePrint->text('remark')
                ->nullable()
                ->after('meta_data')
                ->comment('Optional notes or metadata descriptions for the media file');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('media_files', function (Blueprint $bluePrint) {
            $bluePrint->dropColumn('remark');
        });
    }
}
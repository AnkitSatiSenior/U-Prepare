<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLatLongToMediaFilesAndSubPackageProjectsTables extends Migration
{
    public function up()
    {
        // Add columns to media_files
        Schema::table('media_files', function (Blueprint $table) {
            $table->decimal('lat', 10, 7)->nullable()->after('meta_data');
            $table->decimal('long', 10, 7)->nullable()->after('lat');
        });

        // Add columns to sub_package_projects
        Schema::table('sub_package_projects', function (Blueprint $table) {
            $table->decimal('lat', 10, 7)->nullable()->after('deleted_at');
            $table->decimal('long', 10, 7)->nullable()->after('lat');
        });
    }

    public function down()
    {
        // Drop columns from media_files
        Schema::table('media_files', function (Blueprint $table) {
            $table->dropColumn(['lat', 'long']);
        });

        // Drop columns from sub_package_projects
        Schema::table('sub_package_projects', function (Blueprint $table) {
            $table->dropColumn(['lat', 'long']);
        });
    }
}

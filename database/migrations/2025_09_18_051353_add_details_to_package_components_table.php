<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('package_components', function (Blueprint $table) {
            $table->string('image')->nullable();
            $table->string('page_hin_title')->nullable();
            $table->string('page_eng_title')->nullable();
            $table->text('hin_content')->nullable();
            $table->text('eng_content')->nullable();
            
        });
    }

    public function down()
    {
        Schema::table('package_components', function (Blueprint $table) {
            $table->dropColumn([
                'image',
                'page_hin_title',
                'page_eng_title',
                'hin_content',
                'eng_content',
                
            ]);
        });
    }
};

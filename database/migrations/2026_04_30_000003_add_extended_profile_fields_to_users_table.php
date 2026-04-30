<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('dob')->nullable()->after('district');
            $table->date('date_of_joining')->nullable()->after('dob');
            $table->text('qualification')->nullable()->after('date_of_joining');
            $table->string('total_work_experience')->nullable()->after('qualification');
            $table->text('area_of_expertise')->nullable()->after('total_work_experience');
            $table->text('procurement_support')->nullable()->after('area_of_expertise');
            $table->string('research_publication_citation')->nullable()->after('procurement_support');
            $table->longText('previous_experience')->nullable()->after('research_publication_citation');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'dob',
                'date_of_joining',
                'qualification',
                'total_work_experience',
                'area_of_expertise',
                'procurement_support',
                'research_publication_citation',
                'previous_experience',
            ]);
        });
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            if (!Schema::hasColumn('pages', 'title_hi')) {
                $table->string('title_hi')->nullable()->after('title');
            }

            if (!Schema::hasColumn('pages', 'slug')) {
                $table->string('slug')->unique()->after('title');
            }

            if (!Schema::hasColumn('pages', 'body_eng')) {
                $table->longText('body_eng')->nullable();
            }

            if (!Schema::hasColumn('pages', 'body_hindi')) {
                $table->longText('body_hindi')->nullable();
            }

            if (!Schema::hasColumn('pages', 'meta_title')) {
                $table->string('meta_title')->nullable();
            }

            if (!Schema::hasColumn('pages', 'meta_description')) {
                $table->text('meta_description')->nullable();
            }

            if (!Schema::hasColumn('pages', 'meta_keywords')) {
                $table->string('meta_keywords')->nullable();
            }

            if (!Schema::hasColumn('pages', 'status')) {
                $table->boolean('status')->default(true);
            }

            if (!Schema::hasColumn('pages', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            if (Schema::hasColumn('pages', 'title_hi')) {
                $table->dropColumn('title_hi');
            }

            if (Schema::hasColumn('pages', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};

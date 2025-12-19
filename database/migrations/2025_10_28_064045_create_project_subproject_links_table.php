<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_subproject_links', function (Blueprint $table) {
            $table->id();

            // Project - main package
            $table->unsignedBigInteger('project_id')->nullable();
            $table->foreign('project_id')
                  ->references('id')
                  ->on('package_projects')
                  ->onUpdate('cascade')
                  ->onDelete('set null');

            // Subproject - optional
            $table->unsignedBigInteger('subproject_id')->nullable();
            $table->foreign('subproject_id')
                  ->references('id')
                  ->on('sub_package_projects')
                  ->onUpdate('cascade')
                  ->onDelete('set null');

            // User
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onUpdate('cascade')
                  ->onDelete('set null');

            // Extra fields
            $table->text('remark')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_subproject_links');
    }
};

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
        // Create sub_departments table
        Schema::create('sub_departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')
                  ->constrained('departments')
                  ->onDelete('cascade'); // linked to departments table
            $table->string('name');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // Add sub_department_id to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('sub_department_id')
                  ->nullable()
                  ->after('department_id')
                  ->constrained('sub_departments')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop column first because of foreign key dependency
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['sub_department_id']);
            $table->dropColumn('sub_department_id');
        });

        Schema::dropIfExists('sub_departments');
    }
};

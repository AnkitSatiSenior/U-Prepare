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
        Schema::create('permission_group_routes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('group_id'); // FK to permission_groups
            $table->string('route_name'); // ex: admin.boqentry.create

            $table->foreign('group_id')
                ->references('id')
                ->on('permission_groups')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_group_routes');
    }
};

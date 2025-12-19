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
        Schema::create('already_define_epc', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_service');
            $table->integer('sl_no');
            $table->string('activity_name');
            $table->string('stage_name');
            $table->text('item_description')->nullable();
            $table->decimal('percent', 8, 2)->default(0);
           
            $table->timestamps();
            $table->softDeletes();

            // Uncomment and adjust if foreign key needed:
            $table->foreign('work_service')->references('id')->on('work_service')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('already_define_epc');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('safeguard_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sub_package_project_id');
            $table->unsignedBigInteger('safeguard_compliance_id');
            $table->unsignedBigInteger('contraction_phase_id');

            $table->string('sl_no', 1000);
            $table->text('item_description');

            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('sub_package_project_id')
                  ->references('id')
                  ->on('sub_package_projects')
                  ->onDelete('cascade');

            $table->foreign('safeguard_compliance_id')
                  ->references('id')
                  ->on('safeguard_compliances')
                  ->onDelete('cascade');

            $table->foreign('contraction_phase_id')
                  ->references('id')
                  ->on('contraction_phases')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('safeguard_entries');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('already_define_safeguard_entries', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Foreign Key Columns
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('safeguard_compliance_id')->nullable();
            $table->unsignedBigInteger('contraction_phase_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Main Data Columns
            |--------------------------------------------------------------------------
            */

            $table->text('sl_no')->nullable();

            // ⭐ NEW COLUMN ADDED HERE
            $table->integer('order_by')
                ->default(0)
                ->comment('Sorting order');

            $table->text('item_description')->nullable();
          
            $table->boolean('is_validity')->default(0);
            $table->boolean('is_major_head')->default(0);

            /*
            |--------------------------------------------------------------------------
            | Timestamps + Soft Deletes
            |--------------------------------------------------------------------------
            */

            $table->timestamps();
            $table->softDeletes();

            /*
            |--------------------------------------------------------------------------
            | Foreign Key Constraints
            |--------------------------------------------------------------------------
            */

            $table->foreign('category_id')
                ->references('id')
                ->on('sub_category')
                ->onDelete('set null')
                ->onUpdate('cascade');

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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('already_define_safeguard_entries');
    }
};

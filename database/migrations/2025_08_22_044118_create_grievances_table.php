<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grievances', function (Blueprint $table) {
            $table->id();

            $table->string('full_name');
            $table->text('address')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile', 15)->nullable();

            $table->string('grievance_related_to'); // Required
            $table->string('nature_of_complaint'); // Required
            $table->text('detail_of_complaint')->nullable();

            $table->string('district')->nullable();
            $table->string('block')->nullable();
            $table->string('village')->nullable();
            $table->string('project')->nullable();
            $table->text('description')->nullable();

            $table->string('document')->nullable(); // File path

            $table->boolean('filing_on_behalf')->default(false);
            $table->boolean('consent_from_survivor')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grievances');
    }
};

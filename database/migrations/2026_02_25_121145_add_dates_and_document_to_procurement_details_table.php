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
        Schema::table('procurement_details', function (Blueprint $table) {
            // Technical Evaluation Stage
            $table->date('technical_eval_date')->nullable()->after('publication_date');
            $table->string('technical_eval_document_path')->nullable()->after('technical_eval_date');
            
            // Financial Evaluation Stage
            $table->date('financial_eval_date')->nullable()->after('technical_eval_document_path');
            $table->string('financial_eval_document_path')->nullable()->after('financial_eval_date');
            
            // LOA (Letter of Award) Stage
            $table->date('loa_issued_date')->nullable()->after('financial_eval_document_path');
            $table->string('loa_issued_document_path')->nullable()->after('loa_issued_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('procurement_details', function (Blueprint $table) {
            $table->dropColumn([
                'technical_eval_date', 
                'technical_eval_document_path',
                'financial_eval_date', 
                'financial_eval_document_path',
                'loa_issued_date',
                'loa_issued_document_path'
            ]);
        });
    }
};
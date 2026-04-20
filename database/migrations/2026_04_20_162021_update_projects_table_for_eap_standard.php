<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Master Links
            $table->foreignId('funding_agency_id')->nullable()->constrained('funding_agencies')->onDelete('set null');
            
            // Basic Info (As requested, Department Name is now a string)
            $table->string('department_name')->nullable(); 
            $table->string('project_short_name')->nullable();
            $table->date('loan_signing_date')->nullable();
            $table->date('start_date')->nullable();
            $table->date('scheduled_closure_date')->nullable();
            $table->string('donor_currency', 10)->nullable();
            
            // Financial Outlays (INR & Donor)
            $table->decimal('outlay_inr', 20, 2)->default(0);
            $table->decimal('outlay_donor_currency', 20, 2)->default(0);
            $table->decimal('donor_share_inr', 20, 2)->default(0);
            $table->decimal('donor_share_donor_currency', 20, 2)->default(0);
            $table->decimal('state_share_inr', 20, 2)->default(0);
            $table->decimal('state_share_donor_currency', 20, 2)->default(0);
            $table->decimal('other_share_inr', 20, 2)->default(0);

            // Revisions
            $table->boolean('is_revised')->default(false);
            $table->date('revised_closure_date')->nullable();
            $table->decimal('revised_outlay_inr', 20, 2)->nullable();
            $table->decimal('revised_outlay_donor_currency', 20, 2)->nullable();
            $table->decimal('revised_donor_share_inr', 20, 2)->nullable();
            $table->decimal('revised_donor_share_donor_currency', 20, 2)->nullable();
            $table->decimal('revised_state_share_inr', 20, 2)->nullable();
            $table->decimal('revised_state_share_donor_currency', 20, 2)->nullable();
            $table->decimal('revised_other_share_inr', 20, 2)->nullable();

            // DLI vs Non-DLI Logic
            $table->boolean('is_dli_based')->default(false);
            $table->string('financial_year')->nullable();
            $table->integer('dli_target_count')->nullable();
            $table->decimal('dli_amount_target_donor', 20, 2)->nullable();
            $table->decimal('dli_amount_target_inr', 20, 2)->nullable();
            $table->decimal('ta_amount_reimb_target_donor', 20, 2)->nullable();
            $table->decimal('ta_amount_reimb_target_inr', 20, 2)->nullable();

            // Non-DLI Targets
            $table->decimal('target_expenditure_donor', 20, 2)->nullable();
            $table->decimal('target_expenditure_inr', 20, 2)->nullable();
            $table->decimal('reimbursement_target_donor', 20, 2)->nullable();
            $table->decimal('reimbursement_target_inr', 20, 2)->nullable();

            // Descriptions & Locations
            $table->text('objective')->nullable();
            $table->text('components')->nullable();
            $table->json('implementation_locations')->nullable(); // For Uttarakhand districts
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['funding_agency_id']);
            $table->dropColumn([
                'funding_agency_id', 'department_name', 'project_short_name', 
                'loan_signing_date', 'start_date', 'scheduled_closure_date',
                'donor_currency', 'outlay_inr', 'outlay_donor_currency',
                'is_revised', 'is_dli_based', 'implementation_locations'
                // ... add other columns for a full rollback if needed
            ]);
        });
    }
};
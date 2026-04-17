<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ESCALATION LOGS — MULTI-CATEGORY EXTENSION
 *
 * This migration extends the escalation_logs table to support 4 escalation types:
 *   - social_safeguard  (Pre-Construction pending but During Construction started)
 *   - physical_progress (No BOQ/EPC physical progress beyond expected schedule)
 *   - financial_progress (No financial bill submitted beyond expected interval)
 *   - contract_security  (Security certificate near expiry or already expired)
 *
 * Changes:
 *   1. Add `escalation_category` column to distinguish type
 *   2. Make `compliance_id` nullable (only used by social_safeguard type)
 *   3. Drop old unique constraint, re-add with category in the key
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('escalation_logs', function (Blueprint $table) {

            // 1. Drop the old unique constraint (name must match exactly)
            $table->dropUnique('unique_escalation_guard');

            // 2. Make compliance_id nullable — physical/financial/security don't use it
            $table->unsignedBigInteger('compliance_id')->nullable()->change();

            // 3. Add the new category column AFTER type
            $table->string('escalation_category', 30)
                  ->default('social_safeguard')
                  ->after('type')
                  ->comment('Possible values: social_safeguard, physical_progress, financial_progress, contract_security');

            // 4. Re-add unique constraint with category included
            //    This allows the SAME project to have separate locks per category.
            $table->unique(
                ['escalatable_id', 'escalatable_type', 'escalation_category', 'compliance_id', 'day_mark', 'level'],
                'unique_escalation_guard_v2'
            );
        });
    }

    public function down(): void
    {
        Schema::table('escalation_logs', function (Blueprint $table) {
            $table->dropUnique('unique_escalation_guard_v2');
            $table->dropColumn('escalation_category');
            $table->unsignedBigInteger('compliance_id')->nullable(false)->change();
            $table->unique(
                ['escalatable_id', 'escalatable_type', 'compliance_id', 'day_mark', 'level'],
                'unique_escalation_guard'
            );
        });
    }
};
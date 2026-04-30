<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            // Basic Details
            'name'                      => 'required|string|max:255',
            'project_short_name'        => 'required|string|max:100',
            'funding_agency_id'         => 'required|exists:funding_agencies,id',
            'department_name'           => 'required|string|max:255',
            'loan_signing_date'         => 'required|date',
            'start_date'                => 'required|date',
            'scheduled_closure_date'    => 'required|date|after:start_date',
            'donor_currency'            => 'required|string|max:10',

            // Financials (Original)
            'outlay_inr'                => 'required|numeric|min:0',
            'outlay_donor_currency'     => 'required|numeric|min:0',
            'donor_share_inr'           => 'required|numeric|min:0',
            'donor_share_donor_currency'=> 'required|numeric|min:0',
            'state_share_inr'           => 'required|numeric|min:0',
            'state_share_donor_currency'=> 'required|numeric|min:0',
            'other_share_inr'           => 'required|numeric|min:0',

            // Revised Fields (Conditional)
            'is_revised'                => 'required|boolean',
            'revised_closure_date'      => 'required_if:is_revised,1|nullable|date',
            'revised_outlay_inr'        => 'required_if:is_revised,1|nullable|numeric',
            'revised_outlay_donor_currency'  => 'required_if:is_revised,1|nullable|numeric',
            'revised_donor_share_inr'   => 'required_if:is_revised,1|nullable|numeric',
            'revised_donor_share_donor_currency' => 'required_if:is_revised,1|nullable|numeric',
            'revised_state_share_inr'   => 'required_if:is_revised,1|nullable|numeric',
            'revised_state_share_donor_currency' => 'required_if:is_revised,1|nullable|numeric',
            'revised_other_share_inr'   => 'required_if:is_revised,1|nullable|numeric',

            // DLI vs Non-DLI (Conditional)
            'is_dli_based'              => 'required|boolean',
            'financial_year'            => 'required|string|max:20',
            
            // If DLI is YES
            'dli_target_count'          => 'required_if:is_dli_based,1|nullable|integer|min:0',
            'dli_amount_target_donor'   => 'required_if:is_dli_based,1|nullable|numeric|min:0',
            'dli_amount_target_inr'     => 'required_if:is_dli_based,1|nullable|numeric|min:0',
            'ta_amount_reimb_target_donor' => 'required_if:is_dli_based,1|nullable|numeric|min:0',
            'ta_amount_reimb_target_inr'   => 'required_if:is_dli_based,1|nullable|numeric|min:0',

            // If DLI is NO
            'target_expenditure_donor'  => 'required_if:is_dli_based,0|nullable|numeric',
            'target_expenditure_inr'    => 'required_if:is_dli_based,0|nullable|numeric',
            'reimbursement_target_donor' => 'required_if:is_dli_based,0|nullable|numeric',
            'reimbursement_target_inr'   => 'required_if:is_dli_based,0|nullable|numeric',

            // Descriptive
            'objective'                 => 'required|string',
            'components'                => 'required|string',
            'implementation_locations'  => 'required|array|min:1',
            'implementation_locations.*'=> 'string',
        ];
    }

    public function messages(): array
    {
        return [
            'target_expenditure_donor.required_if' => 'Target Expenditure (Donor) is required when the project is Non-DLI.',
            'target_expenditure_inr.required_if' => 'Target Expenditure (INR) is required when the project is Non-DLI.',
            'reimbursement_target_donor.required_if' => 'Reimbursement Target (Donor) is required when the project is Non-DLI.',
            'reimbursement_target_inr.required_if' => 'Reimbursement Target (INR) is required when the project is Non-DLI.',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Implement Permission/Policy check here
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // 1. Core Information
            'name'                      => 'required|string|max:255',
            'project_short_name'        => 'required|string|max:100',
            'funding_agency_id'         => 'required|exists:funding_agencies,id',
            'department_name'           => 'required|string|max:255',
            'loan_signing_date'         => 'required|date',
            'start_date'                => 'required|date',
            'scheduled_closure_date'    => 'required|date|after_or_equal:start_date',
            'donor_currency'            => 'required|string|max:10',

            // 2. Financial Outlays (Original)
            'outlay_inr'                => 'required|numeric|min:0',
            'outlay_donor_currency'     => 'required|numeric|min:0',
            'donor_share_inr'           => 'required|numeric|min:0',
            'donor_share_donor_currency'=> 'required|numeric|min:0',
            'state_share_inr'           => 'required|numeric|min:0',
            'state_share_donor_currency'=> 'required|numeric|min:0',
            'other_share_inr'           => 'required|numeric|min:0',

            // 3. Revision Logic (Conditional)
            'is_revised'                => 'required|boolean',
            'revised_closure_date'      => 'required_if:is_revised,1|nullable|date|after:scheduled_closure_date',
            'revised_outlay_inr'        => 'required_if:is_revised,1|nullable|numeric|min:0',
            'revised_outlay_donor_currency'  => 'required_if:is_revised,1|nullable|numeric|min:0',
            'revised_donor_share_inr'   => 'required_if:is_revised,1|nullable|numeric|min:0',
            'revised_donor_share_donor_currency' => 'required_if:is_revised,1|nullable|numeric|min:0',
            'revised_state_share_inr'   => 'required_if:is_revised,1|nullable|numeric|min:0',
            'revised_state_share_donor_currency' => 'required_if:is_revised,1|nullable|numeric|min:0',
            'revised_other_share_inr'   => 'required_if:is_revised,1|nullable|numeric|min:0',

            // 4. DLI vs Non-DLI Implementation
            'is_dli_based'              => 'required|boolean',
            'financial_year'            => 'required|string|max:20',
            
            // Required only if DLI is YES
            'dli_target_count'          => 'required_if:is_dli_based,1|nullable|integer|min:0',
            'dli_amount_target_donor'   => 'required_if:is_dli_based,1|nullable|numeric|min:0',
            'dli_amount_target_inr'     => 'required_if:is_dli_based,1|nullable|numeric|min:0',
            'ta_amount_reimb_target_donor' => 'required_if:is_dli_based,1|nullable|numeric|min:0',
            'ta_amount_reimb_target_inr'   => 'required_if:is_dli_based,1|nullable|numeric|min:0',

            // Required only if DLI is NO
            'target_expenditure_donor'  => 'required_if:is_dli_based,0|nullable|numeric|min:0',
            'target_expenditure_inr'    => 'required_if:is_dli_based,0|nullable|numeric|min:0',
            'reimbursement_target_donor' => 'required_if:is_dli_based,0|nullable|numeric|min:0',
            'reimbursement_target_inr'   => 'required_if:is_dli_based,0|nullable|numeric|min:0',

            // 5. Descriptions & Geographical Scope
            'objective'                 => 'required|string',
            'components'                => 'required|string',
            'implementation_locations'  => 'required|array|min:1',
            'implementation_locations.*'=> 'string',
        ];
    }

    /**
     * Custom error messages for clarity.
     */
    public function messages(): array
    {
        return [
            'revised_closure_date.required_if' => 'The Revised Closure Date is required when the project is marked as Revised.',
            'dli_target_count.required_if'    => 'DLI Target Count is mandatory for DLI-based projects.',
            'implementation_locations.required' => 'Please select at least one district for implementation.',
            'target_expenditure_donor.required_if' => 'Target Expenditure (Donor) is required when the project is Non-DLI.',
            'target_expenditure_inr.required_if' => 'Target Expenditure (INR) is required when the project is Non-DLI.',
            'reimbursement_target_donor.required_if' => 'Reimbursement Target (Donor) is required when the project is Non-DLI.',
            'reimbursement_target_inr.required_if' => 'Reimbursement Target (INR) is required when the project is Non-DLI.',
        ];
    }
}

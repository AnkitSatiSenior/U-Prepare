<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePackageProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Project and Category Relations
            'project_id' => ['nullable', 'exists:projects,id'],
            'package_category_id' => ['nullable', 'exists:projects_category,id'],
            'package_sub_category_id' => ['nullable', 'exists:sub_category,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'sub_department_id' => ['nullable', 'exists:sub_departments,id'],
            'package_component_id' => ['nullable', 'exists:package_components,id'],

            // Package Info
            'package_name' => ['required', 'string', 'max:255'],
            'package_number' => ['required', 'string', 'max:100', 'unique:package_projects'],
            'estimated_budget_incl_gst' => ['required', 'numeric', 'min:0'],

            // Geography / Constituencies
            'vidhan_sabha_id' => ['nullable', 'exists:constituencies,id'],
            'lok_sabha_id' => ['nullable', 'exists:assembly,id'],
            'district_id' => ['nullable', 'exists:geography_districts,id'],
            'block_id' => ['nullable', 'exists:geography_blocks,id'],

            // Safeguard
            'safeguard_exists' => ['required', 'boolean'],

            // DEC Approval
            'dec_approved' => ['required', 'boolean'],
            'dec_approval_date' => ['nullable', 'required_if:dec_approved,1', 'date'],
            'dec_letter_number' => ['nullable', 'required_if:dec_approved,1', 'string', 'max:100'],
            'dec_document_path' => ['nullable', 'file', 'mimes:pdf', 'max:20480'],

            // HPC Approval
            'hpc_approved' => ['required', 'boolean'],
            'hpc_approval_date' => ['nullable', 'required_if:hpc_approved,1', 'date'],
            'hpc_letter_number' => ['nullable', 'required_if:hpc_approved,1', 'string', 'max:100'],
            'hpc_document_path' => ['nullable', 'file', 'mimes:pdf', 'max:20480'],

            // Status
            'status' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            // DEC/HPC Document Messages
            'dec_document_path.max' => 'The DEC document must not exceed 20MB.',
            'hpc_document_path.max' => 'The HPC document must not exceed 20MB.',
            'dec_document_path.mimes' => 'The DEC document must be a PDF file.',
            'hpc_document_path.mimes' => 'The HPC document must be a PDF file.',

            // Conditional Required Messages
            'dec_approval_date.required_if' => 'DEC approval date is required when DEC is approved.',
            'dec_letter_number.required_if' => 'DEC letter number is required when DEC is approved.',
            'hpc_approval_date.required_if' => 'HPC approval date is required when HPC is approved.',
            'hpc_letter_number.required_if' => 'HPC letter number is required when HPC is approved.',

            // Generic Required
            'package_name.required' => 'Package name is required.',
            'package_number.required' => 'Package number is required.',
            'estimated_budget_incl_gst.required' => 'Sanctioned cost is required.',
            'safeguard_exists.required' => 'Please indicate if safeguards exist.',
        ];
    }
}

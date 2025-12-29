<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePackageProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('package_project')->id ?? null;

        return [
            'project_id' => ['nullable', 'exists:projects,id'],
            'package_category_id' => ['nullable', 'exists:projects_category,id'],
            'package_sub_category_id' => ['nullable', 'exists:sub_category,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'sub_department_id' => ['nullable', 'exists:sub_departments,id'], // ✅ added
            'package_component_id' => ['nullable', 'exists:package_components,id'],
            'package_name' => ['required', 'string'],
            'safeguard_exists' => ['required', 'boolean'],
            'package_number' => ['required', 'string', 'unique:package_projects,package_number,' . $id],
            'estimated_budget_incl_gst' => ['required', 'numeric', 'min:0'],
            'vidhan_sabha_id' => ['nullable', 'exists:constituencies,id'],
            'lok_sabha_id' => ['nullable', 'exists:assembly,id'],
            'district_id' => ['nullable', 'exists:geography_districts,id'],
            'block_id' => ['nullable', 'exists:geography_blocks,id'],

            'dec_approved' => ['required', 'boolean'],
            'dec_approval_date' => ['nullable', 'date'],
            'dec_letter_number' => ['nullable', 'string', 'max:100'],
            'dec_document_path' => ['nullable', 'file', 'mimes:pdf', 'max:20480'],

            'hpc_approved' => ['required', 'boolean'],
            'hpc_approval_date' => ['nullable', 'date'],
            'hpc_letter_number' => ['nullable', 'string', 'max:100'],
            'hpc_document_path' => ['nullable', 'file', 'mimes:pdf', 'max:20480'],

            'status' => ['nullable', 'string', 'in:Pending for Procurement,Pending for Contract,Pending for Physical Activity,In Progress,Cancel,To be Rebid,Removed'],
        ];
    }

    public function messages(): array
    {
        return [
            'dec_document_path.max' => 'The DEC document must not be larger than 20MB.',
            'hpc_document_path.max' => 'The HPC document must not be larger than 20MB.',
            'dec_document_path.mimes' => 'The DEC document must be a PDF file.',
            'hpc_document_path.mimes' => 'The HPC document must be a PDF file.',
        ];
    }
}

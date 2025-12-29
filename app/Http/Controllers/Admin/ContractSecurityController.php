<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\ContractSecurity;
use App\Models\ContractSecurityType;
use App\Models\ContractSecurityForm;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContractSecurityController extends Controller
{
    public function index(Contract $contract)
    {
        $contractStart = $contract->commencement_date;
        $contractEnd = $contract->revised_completion_date ?? ($contract->actual_completion_date ?? $contract->initial_completion_date);

        // Load securities with relationships
        $securities = $contract
            ->securities()
            ->with(['type', 'form'])
            ->latest()
            ->get();

        // Sum of all security values
        $totalSecurityValue = $securities->sum('value');

        // Compare with contract value
        $needsMoreSecurity = $totalSecurityValue < $contract->contract_value;

        return view('admin.contract_securities.index', compact('contract', 'securities', 'contractStart', 'contractEnd', 'totalSecurityValue', 'needsMoreSecurity'));
    }

    public function create(Contract $contract)
    {
        $types = ContractSecurityType::all();
        $forms = ContractSecurityForm::all();
        return view('admin.contract_securities.create', compact('contract', 'types', 'forms'));
    }

    public function store(Request $request, Contract $contract)
    {
        $validated = $request->validate(
            [
                'security_type_id' => ['required', 'exists:contract_security_types,id'],
                'security_form_id' => ['required', 'exists:contract_security_forms,id'],
                'issue_date' => ['nullable', 'date'],
                'issued_end_date' => ['nullable', 'date', 'after_or_equal:issue_date'],
                'security_number' => ['nullable', 'string', 'max:255', 'unique:contract_securities,security_number'],
                'bank_name' => ['nullable', 'string', 'max:255'],
                'value' => ['nullable', 'numeric', 'min:0'],
                'file_path' => ['required', 'file', 'mimes:pdf,jpg,png', 'max:10240'], // 5MB max
            ],
            [
                'security_type_id.required' => 'Please select a security type.',
                'security_type_id.exists' => 'The selected security type is invalid.',
                'security_form_id.required' => 'Please select a form of security.',
                'security_form_id.exists' => 'The selected form of security is invalid.',
                'issue_date.date' => 'The issue date must be a valid date.',
                'issued_end_date.date' => 'The issued end date must be a valid date.',
                'issued_end_date.after_or_equal' => 'The issued end date must be after or equal to the issue date.',
                'security_number.unique' => 'This security number has already been used. Please use a unique number.',
                'security_number.max' => 'The security number may not be greater than 255 characters.',
                'bank_name.max' => 'The bank name may not be greater than 255 characters.',
                'value.numeric' => 'The amount must be a valid number.',
                'value.min' => 'The amount cannot be negative.',
                'file_path.required' => 'Please upload the security document.',
                'file_path.mimes' => 'The document must be a file of type: pdf, jpg, png.',
                'file_path.max' => 'The document may not be greater than 10MB.',
            ],
        );

        $validated['contract_id'] = $contract->id;

        if ($request->hasFile('file_path')) {
            $validated['file_path'] = $request->file('file_path')->store('securities', 'public');
        }

        ContractSecurity::create($validated);

        return redirect()->route('admin.contracts.securities.index', $contract)->with('success', 'Security created successfully!');
    }

    public function edit(Contract $contract, ContractSecurity $security)
    {
        $types = ContractSecurityType::all();
        $forms = ContractSecurityForm::all();
        return view('admin.contract_securities.edit', compact('contract', 'security', 'types', 'forms'));
    }

    public function update(Request $request, Contract $contract, ContractSecurity $security)
    {
        $validated = $request->validate(
            [
                'security_type_id' => ['required', 'exists:contract_security_types,id'],
                'security_form_id' => ['required', 'exists:contract_security_forms,id'],
                'issue_date' => ['nullable', 'date'],
                'issued_end_date' => ['nullable', 'date', 'after_or_equal:issue_date'],
                'security_number' => ['nullable', 'string', 'max:255', Rule::unique('contract_securities', 'security_number')->ignore($security->id)],
                'bank_name' => ['nullable', 'string', 'max:255'],
                'value' => ['nullable', 'numeric', 'min:0'],
                'file_path' => ['nullable', 'file', 'mimes:pdf,jpg,png', 'max:10240'],
            ],
            [
                'security_type_id.required' => 'Please select a security type.',
                'security_type_id.exists' => 'The selected security type is invalid.',
                'security_form_id.required' => 'Please select a form of security.',
                'security_form_id.exists' => 'The selected form of security is invalid.',
                'issue_date.date' => 'The issue date must be a valid date.',
                'issued_end_date.date' => 'The issued end date must be a valid date.',
                'issued_end_date.after_or_equal' => 'The issued end date must be after or equal to the issue date.',
                'security_number.unique' => 'This security number has already been used. Please use a unique number.',
                'security_number.max' => 'The security number may not be greater than 255 characters.',
                'bank_name.max' => 'The bank name may not be greater than 255 characters.',
                'value.numeric' => 'The amount must be a valid number.',
                'value.min' => 'The amount cannot be negative.',
                'file_path.mimes' => 'The document must be a file of type: pdf, jpg, png.',
                'file_path.max' => 'The document may not be greater than 10MB.',
            ],
        );

        if ($request->hasFile('file_path')) {
            $validated['file_path'] = $request->file('file_path')->store('securities', 'public');
        }

        $security->update($validated);

        return redirect()->route('admin.contracts.securities.index', $contract)->with('success', 'Security updated successfully!');
    }

    public function destroy(Contract $contract, ContractSecurity $security)
    {
        $security->delete();

        return redirect()->route('admin.contracts.securities.index', $contract)->with('success', 'Security deleted successfully!');
    }
}

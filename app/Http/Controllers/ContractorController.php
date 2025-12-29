<?php

namespace App\Http\Controllers;


use App\Models\Contractor;
use Illuminate\Http\Request;

class ContractorController extends Controller
{
    public function index()
    {
        $contractors = Contractor::paginate(15);
        return view('admin.contractors.index', compact('contractors'));
    }

    public function create()
    {
        return view('admin.contractors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'authorized_personnel_name' => 'required|string|max:255',

            // nullable + unique only if present
            'phone' => ['nullable', 'string', 'max:20', 'unique:contractors,phone'],
            'email' => ['nullable', 'email', 'unique:contractors,email'],
            'gst_no' => ['nullable', 'string', 'max:50', 'unique:contractors,gst_no'],

            'address' => 'nullable|string|max:500',
        ]);

        Contractor::create($validated);

        return redirect()->route('admin.contractors.index')->with('success', 'Contractor created successfully.');
    }

    public function show(Contractor $contractor)
    {
        return view('admin.contractors.show', compact('contractor'));
    }

    public function edit(Contractor $contractor)
    {
        return view('admin.contractors.edit', compact('contractor'));
    }

    public function update(Request $request, Contractor $contractor)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'authorized_personnel_name' => 'required|string|max:255',

            // On update ignore current record id for unique check
            'phone' => ['nullable', 'string', 'max:20', 'unique:contractors,phone,' . $contractor->id],
            'email' => ['nullable', 'email', 'unique:contractors,email,' . $contractor->id],
            'gst_no' => ['nullable', 'string', 'max:50', 'unique:contractors,gst_no,' . $contractor->id],

            'address' => 'nullable|string|max:500',
        ]);

        $contractor->update($validated);

        return redirect()->route('admin.contractors.index')->with('success', 'Contractor updated successfully.');
    }

    public function destroy(Contractor $contractor)
    {
        $contractor->delete();
        return redirect()->route('admin.contractors.index')->with('success', 'Contractor deleted successfully.');
    }
}

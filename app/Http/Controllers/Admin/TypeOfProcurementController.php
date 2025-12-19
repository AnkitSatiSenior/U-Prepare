<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TypeOfProcurement;
use Illuminate\Http\Request;

class TypeOfProcurementController extends Controller
{
    // Display a listing of the resource
    public function index()
    {
        $procurements = TypeOfProcurement::latest()->withTrashed()->get();
        return view('admin.type_of_procurements.index', compact('procurements'));
    }

    // Show the form for creating a new resource
    public function create()
    {
        return view('admin.type_of_procurements.create');
    }

    // Store a newly created resource in storage
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        TypeOfProcurement::create($request->all());

        return redirect()->route('admin.type-of-procurements.index')
            ->with('success', 'Type of Procurement created successfully.');
    }

    // Show the form for editing the specified resource
    public function edit(TypeOfProcurement $typeOfProcurement)
    {
        return view('admin.type_of_procurements.edit', compact('typeOfProcurement'));
    }

    // Update the specified resource in storage
    public function update(Request $request, TypeOfProcurement $typeOfProcurement)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $typeOfProcurement->update($request->all());

        return redirect()->route('admin.type-of-procurements.index')
            ->with('success', 'Type of Procurement updated successfully.');
    }

    // Soft delete the specified resource
    public function destroy(TypeOfProcurement $typeOfProcurement)
    {
        $typeOfProcurement->delete();

        return redirect()->route('admin.type-of-procurements.index')
            ->with('success', 'Type of Procurement deleted successfully.');
    }

    // Restore a soft deleted resource
    public function restore($id)
    {
        $procurement = TypeOfProcurement::withTrashed()->findOrFail($id);
        $procurement->restore();

        return redirect()->route('admin.type-of-procurements.index')
            ->with('success', 'Type of Procurement restored successfully.');
    }
}

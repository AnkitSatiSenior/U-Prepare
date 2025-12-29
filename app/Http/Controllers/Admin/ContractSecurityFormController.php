<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContractSecurityForm;
use Illuminate\Http\Request;

class ContractSecurityFormController extends Controller
{
    public function index()
    {
        $forms = ContractSecurityForm::latest()->get();
        return view('admin.contract_security_forms.index', compact('forms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:contract_security_forms|max:255',
        ]);

        $form = ContractSecurityForm::create($request->only('name'));

        if ($request->ajax()) {
            return response()->json(['success' => '✅ Form added successfully!', 'form' => $form]);
        }

        return redirect()->back()->with('success', '✅ Form added successfully!');
    }

    public function update(Request $request, $id)
    {
        $form = ContractSecurityForm::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:contract_security_forms,name,' . $form->id . '|max:255',
        ]);

        $form->update($request->only('name'));

        if ($request->ajax()) {
            return response()->json(['success' => '✏️ Form updated successfully!', 'form' => $form]);
        }

        return redirect()->back()->with('success', '✏️ Form updated successfully!');
    }

    public function destroy(Request $request, $id)
    {
        $form = ContractSecurityForm::findOrFail($id);
        $form->delete();

        if ($request->ajax()) {
            return response()->json(['success' => '🗑️ Form deleted successfully!', 'id' => $id]);
        }

        return redirect()->back()->with('success', '🗑️ Form deleted successfully!');
    }

    public function show($id)
    {
        return ContractSecurityForm::findOrFail($id);
    }
}

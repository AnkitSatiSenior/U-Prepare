<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContractSecurityType;
use Illuminate\Http\Request;

class ContractSecurityTypeController extends Controller
{
    public function index()
    {
        $types = ContractSecurityType::all();
        return view('admin.contract_security_types.index', compact('types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:contract_security_types,name|max:255',
        ]);

        $type = ContractSecurityType::create(['name' => $request->name]);
        return response()->json($type, 201);
    }

    public function update(Request $request, $id)
    {
        $type = ContractSecurityType::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:contract_security_types,name,' . $type->id . '|max:255',
        ]);

        $type->update(['name' => $request->name]);
        return response()->json($type);
    }

    public function destroy($id)
    {
        $type = ContractSecurityType::findOrFail($id);
        $type->delete();
        return response()->json(null, 204);
    }
}

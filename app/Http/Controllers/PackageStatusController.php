<?php

namespace App\Http\Controllers;

use App\Models\PackageStatus;
use Illuminate\Http\Request;

class PackageStatusController extends Controller
{
    public function index()
    {
        // Fetch statuses ordered by your new order_by column
        $statuses = PackageStatus::orderBy('order_by')->get();
        return view('package-statuses.index', compact('statuses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'order_by' => 'required|integer',
        ]);

        PackageStatus::create([
            'name' => $request->name,
            'order_by' => $request->order_by,
            // Checkbox only sends a value if checked
            'is_active' => $request->has('is_active') 
        ]);

        return redirect()->back()->with('success', 'Status created successfully.');
    }

    public function update(Request $request, PackageStatus $packageStatus)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'order_by' => 'required|integer',
        ]);

        $packageStatus->update([
            'name' => $request->name,
            'order_by' => $request->order_by,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->back()->with('success', 'Status updated successfully.');
    }

    public function destroy(PackageStatus $packageStatus)
    {
        $packageStatus->delete(); // Soft deletes as per your model
        return redirect()->back()->with('success', 'Status deleted successfully.');
    }
}
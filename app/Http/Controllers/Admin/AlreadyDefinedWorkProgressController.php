<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlreadyDefinedWorkProgress;
use App\Models\WorkService;
use Illuminate\Http\Request;

class AlreadyDefinedWorkProgressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $workProgress = AlreadyDefinedWorkProgress::with('workService')->latest()->get();
        return view('admin.already_defined_work_progress.index', compact('workProgress'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $workServices = WorkService::all();
        return view('admin.already_defined_work_progress.create', compact('workServices'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'work_service_id' => 'required|exists:work_service,id',
            'work_component' => 'required|string|max:255',
            'type_details' => 'nullable|string|max:255',
            'side_location' => 'nullable|string|max:255',
        ]);

        AlreadyDefinedWorkProgress::create($request->all());

        return redirect()->route('admin.already_defined_work_progress.index')
                         ->with('success', 'Work Component created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AlreadyDefinedWorkProgress $alreadyDefinedWorkProgress)
    {
        $workServices = WorkService::all();
        return view('admin.already_defined_work_progress.edit', compact('alreadyDefinedWorkProgress', 'workServices'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AlreadyDefinedWorkProgress $alreadyDefinedWorkProgress)
    {
        $request->validate([
            'work_service_id' => 'required|exists:work_service,id',
            'work_component' => 'required|string|max:255',
            'type_details' => 'nullable|string|max:255',
            'side_location' => 'nullable|string|max:255',
        ]);

        $alreadyDefinedWorkProgress->update($request->all());

        return redirect()->route('admin.already_defined_work_progress.index')
                         ->with('success', 'Work Component updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AlreadyDefinedWorkProgress $alreadyDefinedWorkProgress)
    {
        $alreadyDefinedWorkProgress->delete();
        return redirect()->route('admin.already_defined_work_progress.index')
                         ->with('success', 'Work Component deleted successfully.');
    }
}

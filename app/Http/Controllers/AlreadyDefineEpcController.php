<?php

namespace App\Http\Controllers;

use App\Models\AlreadyDefineEpc;
use App\Models\WorkService;
use App\Models\EpcActivityName;
use Illuminate\Http\Request;

class AlreadyDefineEpcController extends Controller
{
    public function index()
    {
        $items = AlreadyDefineEpc::with(['workService', 'activityName'])->get();
        return view('admin.already_define_epc.index', compact('items'));
    }

    public function create()
    {
        $workServices = WorkService::all();
        $activities = EpcActivityName::orderBy('name')->get();

        return view('admin.already_define_epc.create', compact('workServices', 'activities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'work_service' => 'required|exists:work_service,id',
            'sl_no' => 'required|integer',
            'activity_id' => 'nullable|exists:epc_activity_names,id',
            'new_activity_name' => 'nullable|string|max:255',
            'stage_name' => 'required|string|max:255',
            'item_description' => 'nullable|string',
            'percent' => 'required|numeric|min:0',
        ]);

        // If no existing activity selected but new activity name provided, create it
        if (!$request->activity_id && $request->filled('new_activity_name')) {
            $newActivity = EpcActivityName::create([
                'name' => $request->new_activity_name,
            ]);
            $activityId = $newActivity->id;
        } else {
            $activityId = $request->activity_id;
        }

        AlreadyDefineEpc::create([
            'work_service' => $request->work_service,
            'sl_no' => $request->sl_no,
            'activity_id' => $activityId,
            'stage_name' => $request->stage_name,
            'item_description' => $request->item_description,
            'percent' => $request->percent,
        ]);

        return redirect()->route('admin.already_define_epc.index')->with('success', 'Entry created successfully.');
    }

    public function edit(AlreadyDefineEpc $alreadyDefineEpc)
    {
        $workServices = WorkService::all();
        $activities = EpcActivityName::orderBy('name')->get();

        return view('admin.already_define_epc.edit', compact('alreadyDefineEpc', 'workServices', 'activities'));
    }

    public function update(Request $request, AlreadyDefineEpc $alreadyDefineEpc)
    {
        $request->validate([
            'work_service' => 'required|exists:work_service,id',
            'sl_no' => 'required|integer',
            'activity_id' => 'required|exists:epc_activity_names,id',
            'stage_name' => 'required|string|max:255',
            'item_description' => 'nullable|string',
            'percent' => 'required|numeric|min:0',
        ]);

        $alreadyDefineEpc->update([
            'work_service' => $request->work_service,
            'sl_no' => $request->sl_no,
            'activity_id' => $request->activity_id,
            'stage_name' => $request->stage_name,
            'item_description' => $request->item_description,
            'percent' => $request->percent,
        ]);

        return redirect()->route('admin.already_define_epc.index')->with('success', 'Entry updated successfully.');
    }

    public function destroy(AlreadyDefineEpc $alreadyDefineEpc)
    {
        $alreadyDefineEpc->delete();

        return redirect()->route('admin.already_define_epc.index')->with('success', 'Entry deleted successfully.');
    }

    public function show(AlreadyDefineEpc $alreadyDefineEpc)
    {
        return view('admin.already_define_epc.show', compact('alreadyDefineEpc'));
    }
}

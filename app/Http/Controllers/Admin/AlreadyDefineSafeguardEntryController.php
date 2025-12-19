<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlreadyDefineSafeguardEntry;
use App\Models\SafeguardCompliance;
use App\Models\ContractionPhase;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AlreadyDefineSafeguardEntriesImport;

class AlreadyDefineSafeguardEntryController extends Controller
{
    // ========================= INDEX =========================
    public function index()
    {
        // Group entries by compliance + phase + description + validity + major head
        $entries = AlreadyDefineSafeguardEntry::with(['safeguardCompliance', 'contractionPhase', 'category'])
            ->select(DB::raw('MIN(id) as id'), 'sl_no', 'item_description', 'safeguard_compliance_id', 'contraction_phase_id', 'category_id', 'is_validity', 'is_major_head', 'order_by', DB::raw('COUNT(*) as total_entries'))
            ->groupBy('sl_no', 'item_description', 'safeguard_compliance_id', 'contraction_phase_id', 'category_id', 'is_validity', 'is_major_head', 'order_by')
            ->orderBy('order_by', 'ASC')
            ->get();

        $safeguardCompliances = SafeguardCompliance::all();
        $contractionPhases = ContractionPhase::all();
        $categories = SubCategory::orderBy('name')->get();

        return view('admin.already-define-safeguards.index', compact('entries', 'safeguardCompliances', 'contractionPhases', 'categories'));
    }

    // ========================= EDIT =========================
    public function edit($id)
    {
        $entry = AlreadyDefineSafeguardEntry::findOrFail($id);
        $compliances = SafeguardCompliance::all();
        $phases = ContractionPhase::all();
        $categories = SubCategory::orderBy('name')->get();

        return view('admin.already-define-safeguards.edit', compact('entry', 'compliances', 'phases', 'categories'));
    }

    // ========================= UPDATE =========================
    // ========================= UPDATE =========================
    public function update(Request $request, $id)
    {
        $entry = AlreadyDefineSafeguardEntry::findOrFail($id);

        $request->validate([
            'safeguard_compliance_id' => 'required|exists:safeguard_compliances,id',
            'contraction_phase_id' => 'required|exists:contraction_phases,id',
            'category_id' => 'nullable|exists:sub_category,id',
            'sl_no' => 'nullable|string|max:255',
            'item_description' => 'nullable|string',
            'order_by' => 'nullable|integer',
            'is_validity' => 'nullable|boolean',
            'is_major_head' => 'nullable|boolean',
            'is_parent' => 'nullable|boolean', // ✅ NEW
        ]);

        DB::transaction(function () use ($entry, $request) {
            // 1️⃣ UPDATE AlreadyDefineSafeguardEntries GROUP
            AlreadyDefineSafeguardEntry::where('safeguard_compliance_id', $entry->safeguard_compliance_id)
                ->where('contraction_phase_id', $entry->contraction_phase_id)
                ->where('item_description', $entry->item_description)
                ->update([
                    'safeguard_compliance_id' => $request->safeguard_compliance_id,
                    'contraction_phase_id' => $request->contraction_phase_id,
                    'category_id' => $request->category_id,
                    'sl_no' => $request->sl_no,
                    'item_description' => $request->item_description,
                    'order_by' => $request->order_by,
                    'is_validity' => $request->has('is_validity') ? 1 : 0,
                    'is_major_head' => $request->has('is_major_head') ? 1 : 0,
                    'is_parent' => $request->has('is_parent') ? 1 : 0, // ✅ NEW
                ]);

            // 2️⃣ UPDATE safeguard_entries table
            
        });

        return redirect()->route('admin.already-define-safeguards.index')->with('success', 'Safeguard entries updated successfully!');
    }

    // ========================= DELETE =========================
    public function destroy($id)
    {
        $entry = AlreadyDefineSafeguardEntry::findOrFail($id);

        DB::transaction(function () use ($entry) {
            AlreadyDefineSafeguardEntry::where('safeguard_compliance_id', $entry->safeguard_compliance_id)->where('contraction_phase_id', $entry->contraction_phase_id)->where('item_description', $entry->item_description)->delete();
        });

        return redirect()->route('admin.already-define-safeguards.index')->with('success', 'Safeguard entries deleted successfully!');
    }

    // ========================= IMPORT =========================
    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,csv',
            'safeguard_compliance_id' => 'required|integer',
            'contraction_phase_id' => 'required|integer',
            'category_id' => 'nullable|integer',
        ]);

        Excel::import(new AlreadyDefineSafeguardEntriesImport($request->safeguard_compliance_id, $request->contraction_phase_id, $request->category_id), $request->file('excel_file'));

        return redirect()->back()->with('success', 'Safeguard entries imported successfully!');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\SafeguardEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SafeguardGlobalFunctionController extends Controller
{
    /**
     * Bulk Update entries (updates item_description too).
     */
    public function update(Request $request, $id)
    {
        $entry = SafeguardEntry::findOrFail($id);

        DB::transaction(function () use ($entry, $request) {
            SafeguardEntry::where('contraction_phase_id', $entry->contraction_phase_id)
                ->where('safeguard_compliance_id', $entry->safeguard_compliance_id)
                ->where('item_description', $entry->item_description)
                ->update([
                    'is_validity'      => $request->input('is_validity', $entry->is_validity),
                    'is_major_head'    => $request->input('is_major_head', $entry->is_major_head), // ✅ added
                    'sl_no'            => $request->input('sl_no', $entry->sl_no),
                    'item_description' => $request->input('item_description', $entry->item_description),
                ]);
        });

        return redirect()->route('admin.safeguard-global.index')
            ->with('success', 'Entries updated successfully.');
    }

    /**
     * Bulk Delete entries (whole group).
     */
    public function destroy($id)
    {
        $entry = SafeguardEntry::findOrFail($id);

        DB::transaction(function () use ($entry) {
            SafeguardEntry::where('contraction_phase_id', $entry->contraction_phase_id)
                ->where('safeguard_compliance_id', $entry->safeguard_compliance_id)
                ->where('item_description', $entry->item_description)
                ->delete();
        });

        return redirect()->route('admin.safeguard-global.index')
            ->with('success', 'Entries deleted successfully.');
    }

    /**
     * Show entries list with grouping.
     */
    public function index()
    {
        $entries = SafeguardEntry::with(['safeguardCompliance', 'contractionPhase'])
            ->select(
                DB::raw('MIN(id) as id'),
                'sl_no',
                'item_description',
                'safeguard_compliance_id',
                'contraction_phase_id',
                'is_validity',
                'is_major_head', // ✅ include for grouping & view
                DB::raw('COUNT(*) as total_entries')
            )
            ->groupBy(
                'sl_no',
                'item_description',
                'safeguard_compliance_id',
                'contraction_phase_id',
                'is_validity',
                'is_major_head' // ✅ group by this too
            )
            ->orderBy('sl_no')
            ->get();

        return view('admin.safeguard.index', compact('entries'));
    }

    /**
     * Edit form for an entry.
     */
    public function edit($id)
    {
        $entry = SafeguardEntry::findOrFail($id);

        // Load all entries of this group
        $groupEntries = SafeguardEntry::with('subPackageProject')
            ->where('sl_no', $entry->sl_no)
            ->where('item_description', $entry->item_description)
            ->where('safeguard_compliance_id', $entry->safeguard_compliance_id)
            ->where('contraction_phase_id', $entry->contraction_phase_id)
            ->get();

        return view('admin.safeguard.edit', compact('entry', 'groupEntries'));
    }
}

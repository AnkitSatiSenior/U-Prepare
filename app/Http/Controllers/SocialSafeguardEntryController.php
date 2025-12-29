<?php
namespace App\Http\Controllers;

use App\Models\SafeguardEntry;
use App\Models\SocialSafeguardEntry;
use App\Models\SubPackageProject;
use App\Models\SafeguardCompliance;
use App\Models\AlreadyDefineSafeguardEntry;
use App\Models\ContractionPhase;
use App\Models\Contract;
use App\Models\MediaFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SocialSafeguardEntryController extends Controller
{
    /**
     * List social safeguard entries for a project & compliance
     */
    public function index(int $project_id, int $compliance_id, int $phase_id = null, Request $request)
    {
        $subProject = SubPackageProject::findOrFail($project_id);
        $compliance = SafeguardCompliance::findOrFail($compliance_id);

        $phase_id ??= $compliance->contractionPhases()->first()?->id ?? 1;
        $selectedDate = $request->input('date_of_entry', now()->format('Y-m-d'));

        // Fetch MASTER entries
        $entries = AlreadyDefineSafeguardEntry::with(['safeguardCompliance', 'contractionPhase', 'category'])
            ->where('safeguard_compliance_id', $compliance_id)
            ->where('contraction_phase_id', $phase_id)
            ->orderBy('order_by', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // Fetch all social entries for this project, compliance, phase, and <= selected date
        $socialEntries = SocialSafeguardEntry::where('sub_package_project_id', $project_id)->where('social_compliance_id', $compliance_id)->where('contraction_phase_id', $phase_id)->whereDate('date_of_entry', '<=', $selectedDate)->get()->groupBy('already_define_safeguard_entry_id');

        // Attach the latest social entry to each master entry
        $entries->each(function ($entry) use ($socialEntries) {
            if (isset($socialEntries[$entry->id])) {
                // pick the latest entry by date
                $entry->social = $socialEntries[$entry->id]->sortByDesc('date_of_entry')->first();
            } else {
                $entry->social = null;
            }
            $entry->has_entry = $entry->social ? true : false;
        });

        return view('admin.social_safeguard_entries.index', compact('entries', 'subProject', 'compliance', 'phase_id', 'selectedDate'));
    }

    protected function processMasterEntries($entries, string $selectedDate)
    {
        return $entries->map(function ($entry) use ($selectedDate) {
            // Normalize fields expected by Blade
            $entry->yes_no = $entry->social_entries->first()->yes_no ?? null;
            $entry->remarks = $entry->social_entries->first()->remarks ?? null;
            $entry->date_of_entry = $selectedDate;

            // Flags
            $entry->is_filled = $entry->social_entries->isNotEmpty();

            return $entry;
        });
    }

    public function indexReport(Request $request, int $project_id, int $compliance_id, int $phase_id = null)
    {
        $subProject = SubPackageProject::findOrFail($project_id);
        $compliance = SafeguardCompliance::findOrFail($compliance_id);

        // Authorization check
        $this->authorizeComplianceAccess($subProject, $compliance);

        $phase_id ??= $compliance->contractionPhases()->first()?->id ?? 1;
        $selectedDate = $request->input('date_of_entry', now()->format('Y-m-d'));

        $entries = SafeguardEntry::with(['definedSafeguard', 'socialSafeguardEntries', 'contractionPhase'])

            ->where([
                'sub_package_project_id' => $project_id,
                'safeguard_compliance_id' => $compliance_id,
                'contraction_phase_id' => $phase_id,
            ])
            ->orderBy('order_by', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $entries = $this->processEntries($entries, $selectedDate);

        return view('admin.social_safeguard_entries.index-report', compact('entries', 'subProject', 'compliance', 'phase_id', 'selectedDate'));
    }

   public function reportSummary(int $project_id, int $compliance_id, int $phase_id, Request $request)
{
    $subProject = SubPackageProject::findOrFail($project_id);
    $compliance = SafeguardCompliance::findOrFail($compliance_id);

    $start = $request->filled('start_date') ? Carbon::parse($request->input('start_date')) : now()->startOfYear();
    $end = $request->filled('end_date') ? Carbon::parse($request->input('end_date')) : now();

    if ($start->gt($end)) {
        [$start, $end] = [$end, $start];
    }

    $phase = ContractionPhase::findOrFail($phase_id);
    $isOneTime = $phase->is_one_time;

    // Fetch all social safeguard entries with master already_define_safeguard_entries
    $entries = SocialSafeguardEntry::with(['masterSafeguard'])
        ->where('sub_package_project_id', $project_id)
        ->where('social_compliance_id', $compliance_id)
        ->where('contraction_phase_id', $phase_id)
        ->orderBy('id')
        ->get();

    $report = [];

    foreach ($entries as $social) {
        $sl = $social->masterSafeguard->sl_no ?? 'N/A';
        $item = $social->masterSafeguard->item_description ?? 'N/A';

        if (!isset($report[$sl])) {
            $report[$sl] = [
                'item' => $item,
                'months' => [],
            ];
        }

        if (empty($social->date_of_entry)) {
            continue;
        }

        $entryDate = Carbon::parse($social->date_of_entry);
        $value = $social->yes_no == 1 || $social->yes_no == 3 ? 1 : 0;

        $cursor = $entryDate->copy()->startOfMonth();

        if (!empty($social->validity_date)) {
            $validityDate = Carbon::parse($social->validity_date);
            $monthEnd = $validityDate->lte($end) ? $validityDate->copy()->endOfMonth() : $end->copy()->endOfMonth();
        } else {
            $monthEnd = $isOneTime ? $end->copy()->endOfMonth() : $entryDate->copy()->endOfMonth();
        }

        while ($cursor <= $monthEnd) {
            $monthKey = $cursor->format('M-Y');
            if (!isset($report[$sl]['months'][$monthKey])) {
                $report[$sl]['months'][$monthKey] = ['value' => $value];
            }

            if (!$isOneTime && empty($social->validity_date)) {
                break;
            }

            $cursor->addMonth();
        }
    }

    // Fill missing months as No
    $monthColumns = [];
    $cursor = $start->copy();
    while ($cursor <= $end) {
        $monthColumns[] = $cursor->format('M-Y');
        foreach ($report as $sl => &$row) {
            $monthKey = $cursor->format('M-Y');
            if (!isset($row['months'][$monthKey])) {
                $row['months'][$monthKey] = ['value' => 0];
            }
        }
        unset($row);
        $cursor->addMonth();
    }

    return view('admin.social_safeguard_entries.report_summary', compact(
        'subProject', 'compliance', 'report', 'monthColumns', 'start', 'end', 'phase_id', 'phase'
    ));
}

    public function dynamicComplianceReport(Request $request)
    {
        $complianceId = $request->input('compliance_id');
        $phaseId = $request->input('phase_id');
        $itemDesc = $request->input('item_description');
        $start = $request->filled('start_date') ? Carbon::parse($request->start_date) : now()->startOfYear();
        $end = $request->filled('end_date') ? Carbon::parse($request->end_date) : now();

        if ($start->gt($end)) {
            [$start, $end] = [$end, $start];
        }

        /** -------------------------------
         * Step 1: Dropdowns
         * ------------------------------- */
        $compliances = SafeguardCompliance::orderBy('name')->get();
        $phases = $complianceId ? ContractionPhase::whereIn('id', SafeguardCompliance::find($complianceId)->contraction_phase_ids ?? [])->get() : collect();

        $items = $complianceId && $phaseId ? AlreadyDefineSafeguardEntry::where('safeguard_compliance_id', $complianceId)->where('contraction_phase_id', $phaseId)->when($itemDesc, fn($q) => $q->where('item_description', $itemDesc))->pluck('item_description')->unique() : collect();

        /** -------------------------------
         * Step 2: Month Columns
         * ------------------------------- */
        $monthColumns = [];
        $cursor = $start->copy();
        while ($cursor <= $end) {
            $monthColumns[] = $cursor->format('M-Y');
            $cursor->addMonth();
        }

        /** -------------------------------
         * Step 3: Master Entries
         * ------------------------------- */
        $masterEntries = AlreadyDefineSafeguardEntry::when($complianceId, fn($q) => $q->where('safeguard_compliance_id', $complianceId))->when($phaseId, fn($q) => $q->where('contraction_phase_id', $phaseId))->when($itemDesc, fn($q) => $q->where('item_description', $itemDesc))->orderBy('order_by')->get();

        /** -------------------------------
         * Step 4: Social Entries
         * ------------------------------- */
        $subPackages = SubPackageProject::with('packageProject')->get();
        $subPackageIds = $subPackages->pluck('id');

        $socialEntries = \DB::table('social_safeguard_entries')->whereIn('sub_package_project_id', $subPackageIds)->when($complianceId, fn($q) => $q->where('social_compliance_id', $complianceId))->when($phaseId, fn($q) => $q->where('contraction_phase_id', $phaseId))->get()->groupBy('sub_package_project_id')->map(fn($group) => $group->groupBy('already_define_safeguard_entry_id'));

        $phasesMap = ContractionPhase::pluck('is_one_time', 'id');

        /** -------------------------------
         * Step 5: Build Report
         * ------------------------------- */
        $report = [];

        foreach ($masterEntries as $master) {
            $isOneTime = $phasesMap[$master->contraction_phase_id] ?? false;

            foreach ($subPackages as $subPkg) {
                $pkgName = optional($subPkg->packageProject)->package_number ?? 'SUB-' . $subPkg->id;

                $subEntries = $socialEntries[$subPkg->id][$master->id] ?? collect();

                $monthData = [];
                foreach ($monthColumns as $month) {
                    $monthStart = Carbon::createFromFormat('M-Y', $month)->startOfMonth();
                    $monthEnd = Carbon::createFromFormat('M-Y', $month)->endOfMonth();

                    $hasYes = $subEntries->contains(function ($entry) use ($monthStart, $monthEnd, $isOneTime) {
                        $entryDate = $entry->date_of_entry ? Carbon::parse($entry->date_of_entry) : null;
                        $validityDate = $entry->validity_date ? Carbon::parse($entry->validity_date) : null;

                        if (!$entryDate) {
                            return false;
                        }

                        if ($isOneTime) {
                            // One-time phase: Yes from entry until validity or forever
                            if ($validityDate) {
                                return $monthEnd->gte($entryDate) && $monthStart->lte($validityDate) && in_array($entry->yes_no, [1, 3]);
                            } else {
                                return $monthEnd->gte($entryDate) && in_array($entry->yes_no, [1, 3]);
                            }
                        } else {
                            // Regular phase: only mark Yes within validity
                            $validTill = $validityDate ?? $entryDate;
                            return $monthEnd->gte($entryDate) && $monthStart->lte($validTill) && in_array($entry->yes_no, [1, 3]);
                        }
                    });

                    $monthData[$month] = $hasYes ? 1 : 0;
                }

                $row = [
                    'package' => $pkgName,
                    'sub_package' => $subPkg->name ?? '—',
                    'sl_no' => $master->sl_no,
                    'item_description' => $master->item_description,
                    'is_parent' => (bool) $master->is_parent,
                    'months' => $monthData,
                ];

                $report[] = $row;
            }
        }

        /** -------------------------------
         * Step 6: Return View
         * ------------------------------- */
        return view('admin.social_safeguard_entries.dynamic_report', compact('compliances', 'phases', 'items', 'report', 'monthColumns', 'complianceId', 'phaseId', 'itemDesc', 'start', 'end'));
    }

    public function gallery(Request $request)
    {
        $validated = $request->validate([
            'sub_package_project_id' => 'required|exists:sub_package_projects,id',
            'safeguard_compliance_id' => 'required|exists:safeguard_compliances,id',
            'contraction_phase_id' => 'nullable|exists:contraction_phases,id',
        ]);

        $subProject = SubPackageProject::findOrFail($validated['sub_package_project_id']);
        $compliance = SafeguardCompliance::findOrFail($validated['safeguard_compliance_id']);
        $phaseId = $validated['contraction_phase_id'] ?? null;

        // Query with relation
        $query = SocialSafeguardEntry::with('safeguardEntry')->where('sub_package_project_id', $subProject->id)->where('social_compliance_id', $compliance->id);

        if ($phaseId) {
            $query->where('contraction_phase_id', $phaseId);
        }

        $entries = $query->orderBy('date_of_entry', 'desc')->get();

        // Group media by date
        $gallery = [];
        foreach ($entries as $entry) {
            $photoIds = is_array($entry->photos_documents_case_studies) ? $entry->photos_documents_case_studies : json_decode($entry->photos_documents_case_studies, true);

            if (!empty($photoIds)) {
                $mediaFiles = MediaFile::whereIn('id', (array) $photoIds)->get();

                if ($mediaFiles->isNotEmpty()) {
                    $dateKey = \Carbon\Carbon::parse($entry->date_of_entry)->format('Y-m-d');
                    $gallery[$dateKey][] = [
                        'entry' => $entry,
                        'media' => $mediaFiles,
                        'remarks' => $entry->remarks,
                        'yes_no' => $entry->yes_no,
                        'item_description' => $entry->safeguardEntry?->item_description,
                    ];
                }
            }
        }

        return view('admin.social_safeguard_entries.gallery', compact('subProject', 'compliance', 'gallery', 'phaseId'));
    }

    /**
     * Custom authorization for safeguard compliance access
     */
    private function authorizeComplianceAccess(SubPackageProject $subProject, SafeguardCompliance $compliance)
    {
        $user = auth()->user();

        // Super admins / Admins
        if (in_array($user->role_id, [1, 2])) {
            return true;
        }

        // Check if this user has been assigned to this project + compliance
        $hasAssignment = \App\Models\UserSafeguardSubpackage::where('user_id', $user->id)->where('sub_package_project_id', $subProject->id)->where('safeguard_compliance_id', $compliance->id)->exists();

        if (!$hasAssignment) {
            abort(403, 'Unauthorized access to this compliance/project.');
        }

        return true;
    }

    public function report(int $project_id, int $compliance_id, Request $request)
    {
        $subProject = SubPackageProject::findOrFail($project_id);
        $compliance = SafeguardCompliance::findOrFail($compliance_id);

        // Handle requested date range
        $requestedStart = $request->input('start_date');
        $requestedEnd = $request->input('end_date');

        // If no date range is provided, default to last month's start and end dates
        if (!$requestedStart && !$requestedEnd) {
            $start = now()->subMonthNoOverflow()->startOfMonth()->startOfDay();
            $end = now()->subMonthNoOverflow()->endOfMonth()->endOfDay();
        } else {
            $start = $requestedStart ? Carbon::parse($requestedStart)->startOfDay() : null;
            $end = $requestedEnd ? Carbon::parse($requestedEnd)->endOfDay() : null;
        }

        // Fetch progress
        $progress = $subProject->socialSafeguardProgress($compliance_id, $start, $end);
        $rawForCompliance = $progress[$compliance_id] ?? null;

        // --- Default empty result ---
        if (!$rawForCompliance) {
            $forCompliance = [
                'phases' => [],
                'total' => 0,
                'done' => 0,
                'percent' => 0.0,
                'start' => $start ? $start->toDateString() : null,
                'end' => $end ? $end->toDateString() : null,
                'monthsInRange' => 0,
            ];
        } else {
            $forCompliance = $rawForCompliance;

            // Normalize numeric fields
            $forCompliance['total'] = (float) ($forCompliance['total'] ?? 0);
            $forCompliance['done'] = (float) ($forCompliance['done'] ?? 0);
            $forCompliance['percent'] = $forCompliance['total'] > 0 ? round(($forCompliance['done'] / $forCompliance['total']) * 100, 2) : 0.0;

            // Normalize each phase row
            $forCompliance['phases'] = array_map(function ($ph) {
                $total = (float) ($ph['total'] ?? 0);
                $done = (float) ($ph['done'] ?? 0);
                return [
                    'phase' => $ph['phase'] ?? '—',
                    'total' => $total,
                    'done' => $done,
                    'percent' => $total > 0 ? round(($done / $total) * 100, 2) : 0.0,
                ];
            }, $forCompliance['phases'] ?? []);
        }

        // --- Final prepared data for the view ---
        $phaseReports = $forCompliance['phases'];
        $overallTotal = $forCompliance['total'];
        $overallDone = $forCompliance['done'];
        $overallPercent = $forCompliance['percent'];
        $startDate = $forCompliance['start'];
        $endDate = $forCompliance['end'];
        $monthsInRange = $forCompliance['monthsInRange'];

        $packageProject = $subProject->packageProject ?? null;
        $contract = $packageProject ? Contract::where('project_id', $packageProject->id)->latest('id')->first() : null;

        // --- Return to view ---
        return view('admin.social_safeguard_entries.report', compact('subProject', 'packageProject', 'contract', 'compliance', 'startDate', 'endDate', 'monthsInRange', 'phaseReports', 'overallTotal', 'overallDone', 'overallPercent'));
    }

   public function reportDetails(int $project_id, int $compliance_id, Request $request)
{
    $subProject = SubPackageProject::findOrFail($project_id);
    $compliance = SafeguardCompliance::findOrFail($compliance_id);

    $packageProject = $subProject->packageProject ?? null;
    $contract = $packageProject ? Contract::where('project_id', $packageProject->id)->first() : null;

    $entries = DB::table('social_safeguard_entries AS sse')
    ->leftJoin('safeguard_entries AS se', 'sse.safeguard_entry_id', '=', 'se.id')
    ->leftJoin('already_define_safeguard_entries AS ade', 'se.nomraline', '=', 'ade.nomraline')
    ->leftJoin('contraction_phases AS cp', 'sse.contraction_phase_id', '=', 'cp.id')
    ->select(
        'sse.id as sse_id',
        'sse.already_define_safeguard_entry_id',
        'se.id as safeguard_entry_id',
        'ade.item_description as master_item_description', // master description
        'sse.yes_no',
        'sse.photos_documents_case_studies',
        'sse.remarks',
        'sse.validity_date',
        'sse.date_of_entry',
        'sse.created_at',
        'sse.updated_at',
        'cp.name as phase_name'
    )
    ->where('sse.sub_package_project_id', $subProject->id)
    ->where('sse.social_compliance_id', $compliance->id)
    ->orderBy('sse.date_of_entry', 'desc')
    ->get();


    return view('admin.social_safeguard_entries.report_details', compact('subProject', 'compliance', 'packageProject', 'contract', 'entries'));
}


    public function destroy($id)
    {
        DB::table('social_safeguard_entries')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Safeguard entry deleted successfully.');
    }

    /**
     * Natural sort & attach social, locked status, gallery
     */

    private function naturalSort(string $aSl, string $bSl): int
    {
        $aParts = explode('.', $aSl);
        $bParts = explode('.', $bSl);

        foreach ($aParts as $i => $part) {
            $aNum = is_numeric($part) ? intval($part) : $part;
            $bNum = $bParts[$i] ?? null;

            if ($bNum === null) {
                return 1;
            }

            $bNum = is_numeric($bNum) ? intval($bNum) : $bNum;

            if ($aNum === $bNum) {
                continue;
            }

            return $aNum < $bNum ? -1 : 1;
        }

        return count($aParts) <=> count($bParts);
    }

    private function computeLocked($entry, $social): bool
    {
        // If no `social` entry exists → not locked
        if (!$social) {
            return false;
        }

        // Do NOT lock if Yes/No is "No" (0) or "N/A" (3)
        if (in_array($social->yes_no, [0, 3])) {
            return false;
        }

        $hasValidity = $entry->is_validity && $social?->validity_date;
        $oneTime = $entry->contractionPhase?->is_one_time ?? false;

        // One-time phases
        if ($oneTime) {
            return $hasValidity ? Carbon::parse($social->validity_date)->isFuture() : true;
        }

        // Normal recurring phases
        return $hasValidity && Carbon::parse($social->validity_date)->isFuture();
    }

    private function loadGallery($social)
    {
        if (!$social?->photos_documents_case_studies) {
            return collect();
        }
        return MediaFile::whereIn('id', $social->photos_documents_case_studies)->get()->map->toLightGallery();
    }

    /**
     * Overview of sub-package projects
     */
    public function subPackageProjectOverview(Request $request)
    {
        $date = $request->date_of_entry ? Carbon::parse($request->date_of_entry)->format('Y-m-d') : now()->format('Y-m-d');

        $subProjects = SubPackageProject::orderBy('name')->get();

        $safeguardCompliances = SafeguardCompliance::orderBy('name')->get();
        $contractionPhases = ContractionPhase::orderBy('name')->get();

        // ✅ MASTER safeguards (same for ALL projects)
        $masterSafeguards = AlreadyDefineSafeguardEntry::where('is_validity', 1)->get()->groupBy('safeguard_compliance_id');

        // ✅ Fetch ALL social safeguard entries once
        $socialEntries = SocialSafeguardEntry::whereDate('date_of_entry', '<=', $date)
            ->get()
            ->groupBy(['sub_package_project_id', 'already_define_safeguard_entry_id']);

        $statusMap = [];
        foreach ($subProjects as $project) {
            foreach ($safeguardCompliances as $compliance) {
                $done = false;

                // master safeguards under this compliance
                $complianceSafeguards = $masterSafeguards[$compliance->id] ?? collect();

                foreach ($complianceSafeguards as $safeguard) {
                    if (isset($socialEntries[$project->id][$safeguard->id])) {
                        $done = true;
                        break; // one hit is enough
                    }
                }

                $statusMap[$project->id][$compliance->id] = $done;
            }
        }
        return view('admin.social_safeguard_entries.overview', compact('subProjects', 'safeguardCompliances', 'contractionPhases', 'statusMap', 'date'));
    }

    private function canAccessCompliance(SafeguardCompliance $compliance): bool
    {
        $userRole = auth()->user()->role_id;
        return $userRole == 1 || $userRole == $compliance->role_id;
    }

    /**
     * Store or update a single social safeguard entry
     */

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'already_define_safeguard_entry_id' => 'required|exists:already_define_safeguard_entries,id',
            'sub_package_project_id' => 'required|exists:sub_package_projects,id',
            'social_compliance_id' => 'required|exists:safeguard_compliances,id',
            'contraction_phase_id' => 'required|exists:contraction_phases,id',
            'yes_no' => 'nullable|string',
            'remarks' => 'nullable|string',
            'validity_date' => 'nullable|date',
            'date_of_entry' => 'nullable|date',
            'photos_documents_case_studies.*' => 'nullable|file',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 'error',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        $validated = $validator->validated();

        $date = $validated['date_of_entry'] ?? now()->format('Y-m-d');

        $social = SocialSafeguardEntry::firstOrNew([
            'already_define_safeguard_entry_id' => $validated['already_define_safeguard_entry_id'],
            'sub_package_project_id' => $validated['sub_package_project_id'],
            'social_compliance_id' => $validated['social_compliance_id'],
            'contraction_phase_id' => $validated['contraction_phase_id'],
            'date_of_entry' => $date,
        ]);

        $mediaIds = $social->photos_documents_case_studies ?? [];

        if ($request->hasFile('photos_documents_case_studies')) {
            foreach ($request->file('photos_documents_case_studies') as $file) {
                $media = MediaFile::create([
                    'path' => $file->store('media_files', 'public'),
                    'type' => $file->getClientMimeType(),
                    'meta_data' => [
                        'name' => $file->getClientOriginalName(),
                    ],
                ]);
                $mediaIds[] = $media->id;
            }
        }

        $social->fill($validated);
        $social->photos_documents_case_studies = $mediaIds;
        $social->date_of_entry = $date;
        $social->save();

        return response()->json([
            'status' => 'success',
            'social_id' => $social->id,
            'message' => 'Safeguard entry saved successfully.',
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'yes_no' => 'nullable|string',
            'remarks' => 'nullable|string',
            'validity_date' => 'nullable|date',
            'date_of_entry' => 'nullable|date',
            'photos_documents_case_studies.*' => 'nullable|file',
        ]);

        // Find entry
        $social = SocialSafeguardEntry::findOrFail($id);

        // Handle uploaded files
        $mediaIds = $social->photos_documents_case_studies ?? [];
        if ($request->hasFile('photos_documents_case_studies')) {
            foreach ($request->file('photos_documents_case_studies') as $file) {
                $media = MediaFile::create([
                    'path' => $file->store('media_files', 'public'),
                    'type' => $file->getClientMimeType(),
                    'meta_data' => ['name' => $file->getClientOriginalName()],
                ]);
                $mediaIds[] = $media->id;
            }
        }

        // Update only the fields user passed
        $social->fill($validated);
        $social->photos_documents_case_studies = $mediaIds;

        $social->save();

        return response()->json([
            'status' => 'success',
            'social_id' => $social->id,
            'message' => 'Entry updated successfully.',
        ]);
    }

    public function destroyMedia($id)
    {
        // Find media by ID
        $media = MediaFile::find($id);

        if (!$media) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Media not found.',
                ],
                404,
            );
        }

        try {
            // Delete file from storage if exists
            if (\Storage::disk('public')->exists($media->path)) {
                \Storage::disk('public')->delete($media->path);
            }

            // Remove references from SocialSafeguardEntry
            SocialSafeguardEntry::whereJsonContains('photos_documents_case_studies', $id)
                ->get()
                ->each(function ($entry) use ($id) {
                    $photos = $entry->photos_documents_case_studies ?? [];
                    $photos = array_values(array_diff($photos, [$id]));
                    $entry->photos_documents_case_studies = $photos;
                    $entry->save();
                });

            // Delete media record
            $media->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'File deleted successfully.',
            ]);
        } catch (\Exception $e) {
            \Log::error('Media deletion failed: ' . $e->getMessage());

            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Failed to delete media.',
                ],
                500,
            );
        }
    }

    /**
     * Global overview
     */
    public function overview(Request $request)
    {
        $date = $request->input('date_of_entry', now()->format('Y-m-d'));

        $subProjects = SubPackageProject::with(['safeguardEntries.socialSafeguardEntries'])
            ->orderBy('name')
            ->get();
        $compliances = SafeguardCompliance::orderBy('name')->get();

        $statusMap = [];
        foreach ($subProjects as $project) {
            foreach ($compliances as $compliance) {
                $done = $project->safeguardEntries->where('safeguard_compliance_id', $compliance->id)->filter(fn($entry) => $entry->socialSafeguardEntries()->whereDate('date_of_entry', '<=', $date)->exists())->count() > 0;

                $statusMap[$project->id][$compliance->id] = $done;
            }
        }

        return view('admin.social_safeguard_entries.overview', compact('subProjects', 'compliances', 'statusMap', 'date'));
    }
}

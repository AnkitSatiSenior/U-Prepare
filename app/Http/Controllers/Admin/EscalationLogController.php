<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EscalationLog;
use App\Services\Escalation\EscalationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class EscalationLogController extends Controller
{
    /**
     * Display escalation logs — filtered by category if requested.
     */
    public function index(Request $request): View
    {
        $category = $request->input('category'); // optional filter

        $query = EscalationLog::with(['escalatable', 'compliance'])->latest();

        if ($category && array_key_exists($category, EscalationLog::categoryLabels())) {
            $query->where('escalation_category', $category);
        }

        // Limit to 1000 rows; DataTables handles pagination on the frontend
        $logs = $query->limit(1000)->get();

        $categoryLabels = EscalationLog::categoryLabels();
        $selectedCategory = $category;

        return view('admin.escalation_logs.index', compact('logs', 'categoryLabels', 'selectedCategory'));
    }

    /**
     * Manually trigger the full escalation engine from the admin UI.
     * Runs all 4 categories: Social, Physical, Financial, Security.
     */
    public function triggerEngine(EscalationService $service): RedirectResponse
    {
        try {
            $service->runFullEngine(); // Runs all 4 categories

            return redirect()
                ->route('admin.escalation_logs.index')
                ->with('success', 'Escalation Engine executed successfully across all categories. Logs have been updated.');

        } catch (\Exception $e) {
            Log::error('Manual Escalation Trigger Failed: ' . $e->getMessage());

            return redirect()
                ->route('admin.escalation_logs.index')
                ->with('error', 'Escalation Engine encountered an error: ' . $e->getMessage());
        }
    }
}
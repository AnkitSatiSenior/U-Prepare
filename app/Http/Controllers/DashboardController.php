<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    protected DashboardService $dashboard;

    public function __construct(DashboardService $dashboard)
    {
        $this->dashboard = $dashboard;
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        if ($request->ajax() && $request->has('draw')) {
            $matrixReport = $this->dashboard->getPackageMatrixReport($user);

            return $this->buildMatrixDataTableResponse($matrixReport);
        }

        $data = $this->dashboard->getDashboardData($user);
        $matrixReport = $this->dashboard->getPackageMatrixReport($user);
        $data['reportStatuses'] = $matrixReport['statuses'];

        return view('admin.dashboard', $data);
    }

    public function statusReportReport(Request $request)
    {
        $user = Auth::user();

        if ($request->ajax() && $request->has('draw')) {
            $matrixReport = $this->dashboard->getPackageMatrixReport($user);

            return $this->buildMatrixDataTableResponse($matrixReport);
        }

        $matrixReport = $this->dashboard->getPackageMatrixReport($user);
        $reportStatuses = $matrixReport['statuses'];

        return view('admin.reports.status-matrix', compact('reportStatuses'));
    }

    public function getDepartmentsStatsOther($scope = 'all')
    {
        return $this->dashboard->getDepartmentOverviewStats($scope);
    }

    private function buildMatrixRows(array $matrixReport): Collection
    {
        return collect($matrixReport['packages'])->map(function ($row) use ($matrixReport) {
            foreach ($matrixReport['statuses'] as $status) {
                $columnName = 'status_' . str($status)->slug();
                $row[$columnName] = in_array($status, $row['history'], true) ? 1 : 0;
            }

            return $row;
        });
    }

    private function buildMatrixDataTableResponse(array $matrixReport)
    {
        return DataTables::of($this->buildMatrixRows($matrixReport))
            ->addIndexColumn()
            ->editColumn('estimated_value', fn ($row) => number_format((float) ($row['estimated_value'] ?? 0), 2))
            ->make(true);
    }
}

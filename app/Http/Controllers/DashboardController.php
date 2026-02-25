<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use App\Models\{Department, PackageComponent, PackageProject, Contract, TypeOfProcurement, SubCategory};
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    protected $dashboard;

    public function __construct(DashboardService $dashboard)
    {
        $this->dashboard = $dashboard;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $scope = $request->input('scope', 'all');

        // 1. INTERCEPT YAJRA AJAX REQUEST
        if ($request->ajax() && $request->has('draw')) {
            $matrixReport = $this->dashboard->getPackageMatrixReport($user);
            
            return DataTables::of(collect($matrixReport['packages']))
                ->addIndexColumn() // Auto-generates DT_RowIndex (S.No)
                ->editColumn('estimated_value', function ($row) {
                    return number_format($row['estimated_value'], 2);
                })
                // No need to inject HTML here. We send the raw history array to the frontend.
                ->make(true);
        }

        // 2. NORMAL PAGE LOAD
        $data = $this->dashboard->getDashboardData($user);

        // We only need the statuses to build the dynamic table headers
        $matrixReport = $this->dashboard->getPackageMatrixReport($user);
        $data['reportStatuses'] = $matrixReport['statuses'];

        $departmentQuery = Department::withProjectAndContractStats()->withFinancialStats();
        if ($scope !== 'all') {
            $departmentQuery->where('id', $scope);
        }
        $data['departmentStats'] = $departmentQuery->get();

        return view('admin.dashboard', $data);
    }
    

    public function statusReportReport(Request $request)
    {
        $user = Auth::user();

        // Extract the new structured report data
        $matrixReport = $this->dashboard->getPackageMatrixReport($user);
        $reportStatuses = $matrixReport['statuses'];
        $reportData     = $matrixReport['packages'];

        return view('admin.reports.status-matrix', compact('reportStatuses', 'reportData'));
    }
    public function getDepartmentsStatsOther($scope = 'all')
    {
        $query = Department::withProjectAndContractStats()->withFinancialStats();
        if ($scope !== 'all') {
            $query->where('id', $scope);
        }
        return $query->get();
    }
}

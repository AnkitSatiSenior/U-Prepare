<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use App\Models\{Department, PackageComponent, PackageProject, Contract, TypeOfProcurement, SubCategory};
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

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
            
            // PRE-FLATTEN THE DATA: Map the statuses as exact 1 or 0 columns
            $packages = collect($matrixReport['packages'])->map(function ($row) use ($matrixReport) {
                foreach ($matrixReport['statuses'] as $status) {
                    $columnName = 'status_' . \Illuminate\Support\Str::slug($status);
                    // Create a dynamic key like 'status_bid-published' => 1 or 0
                    $row[$columnName] = in_array($status, $row['history']) ? 1 : 0;
                }
                return $row;
            });

            // Pass the perfectly flattened collection to Yajra
            return DataTables::of($packages)
                ->addIndexColumn()
                ->editColumn('estimated_value', function ($row) {
                    return number_format($row['estimated_value'], 2);
                })
                ->make(true);
        }

        // ... Normal page load logic remains exactly the same ...
        $data = $this->dashboard->getDashboardData($user);
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

        // 1. INTERCEPT YAJRA AJAX REQUEST (If used on a dedicated report page)
        if ($request->ajax() && $request->has('draw')) {
            $matrixReport = $this->dashboard->getPackageMatrixReport($user);
            
            $dt = DataTables::of(collect($matrixReport['packages']))
                ->addIndexColumn()
                ->editColumn('estimated_value', function ($row) {
                    return number_format($row['estimated_value'], 2);
                });

            foreach ($matrixReport['statuses'] as $status) {
                $columnName = 'status_' . Str::slug($status);
                $dt->orderColumn($columnName, function ($collection, $direction) use ($status) {
                    return $direction === 'asc' 
                        ? $collection->sortBy(fn($row) => in_array($status, $row['history']) ? 1 : 0)
                        : $collection->sortByDesc(fn($row) => in_array($status, $row['history']) ? 1 : 0);
                });
            }

            return $dt->make(true);
        }

        // 2. NORMAL PAGE LOAD
        $matrixReport = $this->dashboard->getPackageMatrixReport($user);
        $reportStatuses = $matrixReport['statuses'];
        
        // Passing an empty array for reportData because Yajra handles the rows now
        $reportData = []; 

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
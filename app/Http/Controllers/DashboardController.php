<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use App\Models\{Department, PackageComponent, PackageProject, Contract, TypeOfProcurement, SubCategory};
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
        
        // Grab 'scope' from the URL parameters, default to 'all'
        $scope = $request->input('scope', 'all'); 

        // 1. Get the main dashboard data (this contains $contractsStatus, etc.)
        $data = $this->dashboard->getDashboardData($user);
        
        // 2. Add the report data to the array
        $data['reportData'] = $this->dashboard->getPackageMatrixReport();

        // 3. Add department stats to the array
        $departmentQuery = Department::withProjectAndContractStats()->withFinancialStats();
        if ($scope !== 'all') {
            $departmentQuery->where('id', $scope);
        }
        $data['departmentStats'] = $departmentQuery->get();

        // 4. Return the view with the combined $data array
        return view('admin.dashboard', $data);
    }
    public function getDepartmentsStatsOther($scope = 'all')
    {
        $query = Department::withProjectAndContractStats()->withFinancialStats();
        if ($scope !== 'all') {
            $query->where('id', $scope);
        }
        return $query->get();
    }
    public function statusReportReport(Request $request)
    {
        // You can pass a specific department ID if filtering for UKFES
        $reportData = $this->dashboard->getPackageMatrixReport();

        return view('admin.reports.status-matrix', compact('reportData'));
    }
}

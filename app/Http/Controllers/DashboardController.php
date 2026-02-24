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
    
    $scope = $request->input('scope', 'all'); 

    $data = $this->dashboard->getDashboardData($user);
    
    // FIX: Pass $user here
    $data['reportData'] = $this->dashboard->getPackageMatrixReport($user);

    $departmentQuery = Department::withProjectAndContractStats()->withFinancialStats();
    if ($scope !== 'all') {
        $departmentQuery->where('id', $scope);
    }
    $data['departmentStats'] = $departmentQuery->get();

    return view('admin.dashboard', $data);
}

public function statusReportReport(Request $request)
{
    // FIX: Get user and pass it to the service
    $user = Auth::user();
    $reportData = $this->dashboard->getPackageMatrixReport($user);

    return view('admin.reports.status-matrix', compact('reportData'));
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

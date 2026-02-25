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

        // Extract the new structured report data
        $matrixReport = $this->dashboard->getPackageMatrixReport($user);
        $data['reportStatuses'] = $matrixReport['statuses'];
        $data['reportData']     = $matrixReport['packages'];

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

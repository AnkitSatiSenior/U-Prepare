<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Services\DashboardService;
use App\Models\{Department, PackageComponent, PackageProject, Contract, TypeOfProcurement, SubCategory};
class DashboardController extends Controller
{
    protected $dashboard;

    public function __construct(DashboardService $dashboard)
    {
        $this->dashboard = $dashboard;
    }

    public function index()
    {
        $user = Auth::user();

        $data = $this->dashboard->getDashboardData($user);

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

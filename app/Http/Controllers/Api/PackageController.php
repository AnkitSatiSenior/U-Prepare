<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PackageProject;
use App\Models\SubPackageProject;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    /**
     * Fetch all packages assigned to the authenticated user
     */
    public function assignedPackages(Request $request)
    {
        $user = $request->user();

        $packages = PackageProject::with([
            'department:id,name',
            'category:id,name',
            'subCategory:id,name',
            'contracts.contractor:id,company_name',
            'contracts.subProjects',
            'subProjects'
        ])
            ->get()
            ->map(function ($pkg) {
                return [
                    'id' => $pkg->id,
                    'package_name' => $pkg->package_name,
                    'status' => $pkg->status,
                    'estimated_budget_incl_gst' => $pkg->estimated_budget_incl_gst,
                    'department' => optional($pkg->department)->name,
                    'category' => optional($pkg->category)->name,
                    'sub_category' => optional($pkg->subCategory)->name,
                    'contracts' => $pkg->contracts->map(
                        fn($c) => [
                            'id' => $c->id,
                            'contract_number' => $c->contract_number,
                            'contract_value' => $c->contract_value,
                            'contractor' => optional($c->contractor)->company_name,
                            'signing_date' => $c->signing_date?->format('Y-m-d'),
                            'commencement_date' => $c->commencement_date?->format('Y-m-d'),
                            'initial_completion_date' => $c->initial_completion_date?->format('Y-m-d'),
                            'revised_completion_date' => $c->revised_completion_date?->format('Y-m-d'),
                            'actual_completion_date' => $c->actual_completion_date?->format('Y-m-d'),
                            'sub_projects' => $c->subProjects->map(
                                fn($sp) => [
                                    'id' => $sp->id,
                                    'name' => $sp->name,
                                    'contract_value' => $sp->contract_value,
                                    'lat' => $sp->lat,
                                    'long' => $sp->long,
                                    'physical_progress' => $sp->physical_progress_percentage,
                                    'financial_progress' => $sp->financial_progress_percentage,
                                    'total_finance_amount' => $sp->total_finance_amount,
                                ],
                            ),
                        ],
                    ),
                ];
            });

        return response()->json([
            'status'   => true,
            'user'     => [
                'id' => $user->id,
                'name' => $user->name,
                'role_id' => $user->role_id,
            ],
            'packages' => $packages,
        ]);
    }

    /**
     * Show all sub-packages (with parent package and contract info)
     */
    public function index()
    {
        $subPackages = SubPackageProject::with([
            'packageProject:id,package_name',
            'procurementDetail.typeOfProcurement:id,name', // 👈 eager load procurement
            'contract:id,contract_number,contract_value,contractor_id',
            'contract.contractor:id,company_name'
        ])
            ->get()
            ->map(function ($sp) {
                return [
                    'id' => $sp->id,
                    'name' => $sp->name,
                    'lat' => $sp->lat,
                    'long' => $sp->long,
                    'contract_value' => $sp->contract_value,
                    'physical_progress' => $sp->physical_progress_percentage,
                    'financial_progress' => $sp->financial_progress_percentage,
                    'total_finance_amount' => $sp->total_finance_amount,
                    'type_of_procurement' => $sp->type_of_procurement_name, // 👈 now available
                ];
            });


        return response()->json([
            'status'       => true,
            'sub_packages' => $subPackages,
        ]);
    }
}

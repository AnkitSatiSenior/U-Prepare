<?php

namespace App\Http\Controllers;

use App\Models\FundingAgency;
use App\Http\Requests\StoreFundingAgencyRequest;
use Illuminate\Http\Request;

class FundingAgencyController extends Controller
{
    public function index()
    {
        $agencies = FundingAgency::latest()->paginate(10);
        return view('admin.funding-agency.index', compact('agencies'));
    }

    public function create()
    {
        return view('admin.funding-agency.create');
    }

    public function store(StoreFundingAgencyRequest $request)
    {
        FundingAgency::create($request->validated());

        return redirect()->route('admin.funding-agency.index')
            ->with('success', 'Funding Agency created successfully.');
    }

    public function edit(FundingAgency $fundingAgency)
    {
        return view('admin.funding-agency.edit', compact('fundingAgency'));
    }

    public function update(StoreFundingAgencyRequest $request, FundingAgency $fundingAgency)
    {
        $fundingAgency->update($request->validated());

        return redirect()->route('admin.funding-agency.index')
            ->with('success', 'Funding Agency updated successfully.');
    }

    public function destroy(FundingAgency $fundingAgency)
    {
        // Safety Check: Prevent deletion if agency is linked to projects
        if ($fundingAgency->projects()->exists()) {
            return back()->with('error', 'Cannot delete. This agency is linked to existing projects.');
        }

        $fundingAgency->delete();
        return redirect()->route('admin.funding-agency.index')
            ->with('success', 'Agency deleted successfully.');
    }
}
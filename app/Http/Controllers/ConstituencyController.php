<?php

namespace App\Http\Controllers;

use App\Models\Constituency;
use Illuminate\Http\Request;

class ConstituencyController extends Controller
{
    public function index()
    {
        $constituencies = Constituency::latest()->paginate(10);
        return view('constituencies.index', compact('constituencies'));
    }

    public function create()
    {
        return view('constituencies.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        Constituency::create($validated);

        return redirect()->route('constituencies.index')->with('success', 'Constituency created successfully.');
    }

    public function edit(Constituency $constituency)
    {
        return view('constituencies.edit', compact('constituency'));
    }

    public function update(Request $request, Constituency $constituency)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        $constituency->update($validated);

        return redirect()->route('constituencies.index')->with('success', 'Constituency updated successfully.');
    }

    public function destroy(Constituency $constituency)
    {
        $constituency->delete();

        return redirect()->route('constituencies.index')->with('success', 'Constituency deleted successfully.');
    }
}

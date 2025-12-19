<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LeaderController extends Controller
{
    public function index()
    {
        $leaders = Leader::orderBy('order')->get();
        return view('admin.leaders.index', compact('leaders'));
    }

    public function create()
    {
        return view('admin.leaders.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'   => 'required|string|max:255',
            'title'  => 'nullable|string|max:255',
            'img'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => 'boolean',
            'order'  => 'nullable|integer',
        ]);

        if ($request->hasFile('img')) {
            $data['img'] = $request->file('img')->store('leaders', 'public');
        }

        Leader::create($data);

        return redirect()->route('admin.leaders.index')->with('success', 'Leader created successfully.');
    }

    public function edit(Leader $leader)
    {
        return view('admin.leaders.edit', compact('leader'));
    }

    public function update(Request $request, Leader $leader)
    {
        $data = $request->validate([
            'name'   => 'required|string|max:255',
            'title'  => 'nullable|string|max:255',
            'img'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => 'boolean',
            'order'  => 'nullable|integer',
        ]);

        if ($request->hasFile('img')) {
            if ($leader->img && Storage::disk('public')->exists($leader->img)) {
                Storage::disk('public')->delete($leader->img);
            }
            $data['img'] = $request->file('img')->store('leaders', 'public');
        }

        $leader->update($data);

        return redirect()->route('admin.leaders.index')->with('success', 'Leader updated successfully.');
    }

    public function destroy(Leader $leader)
    {
        if ($leader->img && Storage::disk('public')->exists($leader->img)) {
            Storage::disk('public')->delete($leader->img);
        }

        $leader->delete();
        return redirect()->route('admin.leaders.index')->with('success', 'Leader deleted successfully.');
    }
}

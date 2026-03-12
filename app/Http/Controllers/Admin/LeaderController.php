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
    public function edit(Leader $leader)
    {
        return view('admin.leaders.edit', compact('leader'));
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
            // 🏗️ ARCHITECTURE FIX: Store directly to 's3' disk.
            // This returns the path (e.g., 'leaders/filename.png')
            $data['img'] = $request->file('img')->store('leaders', 's3');
        }

        Leader::create($data);

        return redirect()->route('admin.leaders.index')->with('success', 'Leader created and uploaded to S3.');
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
            // 🏗️ ARCHITECTURE FIX: Delete the old file from S3 if it exists
            if ($leader->img && Storage::disk('s3')->exists($leader->img)) {
                Storage::disk('s3')->delete($leader->img);
            }

            // Upload new file directly to S3
            $data['img'] = $request->file('img')->store('leaders', 's3');
        }

        $leader->update($data);

        return redirect()->route('admin.leaders.index')->with('success', 'Leader updated on S3 successfully.');
    }

    public function destroy(Leader $leader)
    {
        // 🏗️ ARCHITECTURE FIX: Ensure cleanup of S3 objects to avoid storage costs
        if ($leader->img && Storage::disk('s3')->exists($leader->img)) {
            Storage::disk('s3')->delete($leader->img);
        }

        $leader->delete();
        return redirect()->route('admin.leaders.index')->with('success', 'Leader and S3 media deleted.');
    }
}

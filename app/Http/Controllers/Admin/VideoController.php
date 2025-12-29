<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    public function index()
    {
        $adminVideos = Video::orderBy('order')->get();
        return view('admin.videos.index', compact('adminVideos'));
    }

    public function create()
    {
        return view('admin.videos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'text'   => 'required|string|max:255',
            'link'   => 'required|url',
            'img'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'boolean',
            'order'  => 'integer|min:0',
        ]);

        if ($request->hasFile('img')) {
            $validated['img'] = $request->file('img')->store('videos', 'public');
        }

        Video::create($validated);

        return redirect()->route('admin.videos.index')->with('success', 'Video created successfully.');
    }

    public function edit(Video $video)
    {
        return view('admin.videos.edit', compact('video'));
    }

    public function update(Request $request, Video $video)
    {
        $validated = $request->validate([
            'text'   => 'required|string|max:255',
            'link'   => 'required|url',
            'img'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'boolean',
            'order'  => 'integer|min:0',
        ]);

        if ($request->hasFile('img')) {
            if ($video->img) {
                Storage::disk('public')->delete($video->img);
            }
            $validated['img'] = $request->file('img')->store('videos', 'public');
        }

        $video->update($validated);

        return redirect()->route('admin.videos.index')->with('success', 'Video updated successfully.');
    }

    public function destroy(Video $video)
    {
        if ($video->img) {
            Storage::disk('public')->delete($video->img);
        }

        $video->delete();

        return redirect()->route('admin.videos.index')->with('success', 'Video deleted successfully.');
    }
}

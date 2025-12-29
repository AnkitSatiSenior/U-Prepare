<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SlideController extends Controller
{
    public function index()
    {
        $adminslides = Slide::orderBy('order')->get();
        return view('admin.slides.index', compact('adminslides'));
    }

    public function create()
    {
        return view('admin.slides.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'img'      => 'required|image|mimes:jpeg,png,jpg,gif,webp',
            'head'     => 'nullable|string|max:255',
            'subh'     => 'nullable|string|max:255',
            'btn_text' => 'nullable|string|max:255',
            'link'     => 'nullable|url',
            'order'    => 'nullable|integer',
            'status'   => 'boolean',
        ]);

        // Store image
        if ($request->hasFile('img')) {
            $data['img'] = $request->file('img')->store('slides', 'public');
        }

        Slide::create($data);

        return redirect()->route('admin.slides.index')->with('success', 'Slide created successfully');
    }

    public function edit(Slide $slide)
    {
        return view('admin.slides.edit', compact('slide'));
    }

    public function update(Request $request, Slide $slide)
    {
        $data = $request->validate([
            'img'      => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            'head'     => 'nullable|string|max:255',
            'subh'     => 'nullable|string|max:255',
            'btn_text' => 'nullable|string|max:255',
            'link'     => 'nullable|url',
            'order'    => 'nullable|integer',
            'status'   => 'boolean',
        ]);

        // Handle image update
        if ($request->hasFile('img')) {
            // Delete old image if exists
            if ($slide->img && Storage::disk('public')->exists($slide->img)) {
                Storage::disk('public')->delete($slide->img);
            }
            $data['img'] = $request->file('img')->store('slides', 'public');
        }

        $slide->update($data);

        return redirect()->route('admin.slides.index')->with('success', 'Slide updated successfully');
    }

    public function destroy(Slide $slide)
    {
        // Delete image from storage
        if ($slide->img && Storage::disk('public')->exists($slide->img)) {
            Storage::disk('public')->delete($slide->img);
        }

        $slide->delete();
        return redirect()->route('admin.slides.index')->with('success', 'Slide deleted successfully');
    }
}

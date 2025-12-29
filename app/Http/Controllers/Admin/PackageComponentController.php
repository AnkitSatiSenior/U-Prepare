<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PackageComponent;
use Illuminate\Http\Request;

class PackageComponentController extends Controller
{
    public function index()
    {
        $components = PackageComponent::latest()->get();
        return view('admin.package_components.index', compact('components'));
    }

    public function create()
    {
        return view('admin.package_components.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'budget'           => 'nullable|numeric|min:0',
            'description'      => 'nullable|string',
            'image'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'page_hin_title'   => 'nullable|string|max:255',
            'page_eng_title'   => 'nullable|string|max:255',
            'hin_content'      => 'nullable|string',
            'eng_content'      => 'nullable|string',
        ]);

        $data = $request->only([
            'name',
            'budget',
            'description',
            'page_hin_title',
            'page_eng_title',
            'hin_content',
            'eng_content',
        ]);

        // handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('components', 'public');
        }

        PackageComponent::create($data);

        return redirect()->route('admin.package-components.index')
                         ->with('success', 'Package Component created successfully.');
    }

    public function show(PackageComponent $packageComponent)
    {
        return view('admin.package_components.show', compact('packageComponent'));
    }

    public function edit(PackageComponent $packageComponent)
    {
        return view('admin.package_components.edit', compact('packageComponent'));
    }

    public function update(Request $request, PackageComponent $packageComponent)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'budget'           => 'required|numeric|min:0',
            'description'      => 'nullable|string',
            'image'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'page_hin_title'   => 'nullable|string|max:255',
            'page_eng_title'   => 'nullable|string|max:255',
            'hin_content'      => 'nullable|string',
            'eng_content'      => 'nullable|string',
        ]);

        $data = $request->only([
            'name',
            'budget',
            'description',
            'page_hin_title',
            'page_eng_title',
            'hin_content',
            'eng_content',
        ]);

        // handle image upload (replace old one)
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('components', 'public');
        }

        $packageComponent->update($data);

        return redirect()->route('admin.package-components.index')
                         ->with('success', 'Package Component updated successfully.');
    }

    public function destroy(PackageComponent $packageComponent)
    {
        $packageComponent->delete();

        return redirect()->route('admin.package-components.index')
                         ->with('success', 'Package Component deleted successfully.');
    }
}

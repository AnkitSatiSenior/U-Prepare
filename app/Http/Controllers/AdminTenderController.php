<?php

namespace App\Http\Controllers;

use App\Models\Tender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminTenderController extends Controller
{
    public function index()
    {
        $tenders = Tender::latest()->paginate(10);
        return view('admin.tenders.index', compact('tenders'));
    }
    public function publicIndex()
    {
        $tenders = Tender::latest()->get();
        return view('tenders.index', compact('tenders'));
    }

    public function create()
    {
        return view('admin.tenders.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title_en'      => 'required|string|max:255',
            'title_hi'      => 'required|string|max:255',
            'description_en'=> 'nullable|string',
            'description_hi'=> 'nullable|string',
            'open_date'     => 'required|date',
            'close_date'    => 'required|date|after_or_equal:open_date',
            'file'          => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:4096',
        ]);

        if ($request->hasFile('file')) {
            $validated['file'] = $request->file('file')->store('uploads/tenders', 'public');
        }

        $validated['ip_address'] = $request->ip();

        Tender::create($validated);

        return redirect()->route('admin.tenders.index')->with('success', 'Tender created successfully.');
    }

    public function edit(Tender $tender)
    {
        return view('admin.tenders.edit', compact('tender'));
    }

    public function update(Request $request, Tender $tender)
    {
        $validated = $request->validate([
            'title_en'      => 'required|string|max:255',
            'title_hi'      => 'required|string|max:255',
            'description_en'=> 'nullable|string',
            'description_hi'=> 'nullable|string',
            'open_date'     => 'required|date',
            'close_date'    => 'required|date|after_or_equal:open_date',
            'file'          => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:4096',
        ]);

        if ($request->hasFile('file')) {
            if ($tender->file && Storage::disk('public')->exists($tender->file)) {
                Storage::disk('public')->delete($tender->file);
            }
            $validated['file'] = $request->file('file')->store('uploads/tenders', 'public');
        }

        $tender->update($validated);

        return redirect()->route('admin.tenders.index')->with('success', 'Tender updated successfully.');
    }

    public function destroy(Tender $tender)
    {
        if ($tender->file && Storage::disk('public')->exists($tender->file)) {
            Storage::disk('public')->delete($tender->file);
        }
        $tender->delete();

        return redirect()->route('admin.tenders.index')->with('success', 'Tender deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminNewsController extends Controller
{
    public function index()
    {
        $news = News::latest()->get();
        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title_en' => 'required|string|max:255',
            'title_hi' => 'required|string|max:255',
            'body_en'  => 'nullable|string',
            'body_hi'  => 'nullable|string',
            'file'     => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);

        if ($request->hasFile('file')) {
            $validated['file'] = $request->file('file')->store('uploads/news', 'public');
        }

        $validated['ip_address'] = $request->ip();

        News::create($validated);

        return redirect()->route('admin.news.index')->with('success', 'News created successfully.');
    }

    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $request, News $news)
    {
        $validated = $request->validate([
            'title_en' => 'required|string|max:255',
            'title_hi' => 'required|string|max:255',
            'body_en'  => 'nullable|string',
            'body_hi'  => 'nullable|string',
            'file'     => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);

        if ($request->hasFile('file')) {
            if ($news->file && Storage::disk('public')->exists($news->file)) {
                Storage::disk('public')->delete($news->file);
            }
            $validated['file'] = $request->file('file')->store('uploads/news', 'public');
        }

        $news->update($validated);

        return redirect()->route('admin.news.index')->with('success', 'News updated successfully.');
    }

    public function destroy(News $news)
    {
        if ($news->file && Storage::disk('public')->exists($news->file)) {
            Storage::disk('public')->delete($news->file);
        }
        $news->delete();

        return redirect()->route('admin.news.index')->with('success', 'News deleted successfully.');
    }
    
   public function show(Request $request, News $news)
{
    // Determine language based on first URL segment
    $lang = $request->segment(1) === 'hi' ? 'hi' : 'en';

    // Pass both the news item and language to the view
    return view('news.show', [
        'adminnews' => $news,
        'lang' => $lang,
    ]);
}

public function publicIndex(Request $request)
{
    $allNewspublic = News::latest()->get();
    $lang = $request->segment(1) === 'hi' ? 'hi' : 'en';

    return view('news.index', compact('allNewspublic', 'lang'));
}


}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NewsRequest;
use App\Models\News;
use App\Services\NewsService;
use Illuminate\Http\Request;

class AdminNewsController extends Controller
{
    public function __construct(
        private readonly NewsService $newsService
    ) {}

    public function index()
    {
        $news = News::latest()->get();
        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(NewsRequest $request)
    {
        $this->newsService->createNews(
            $request->validated(),
            $request->file('file'),
            $request->ip()
        );

        return redirect()->route('admin.news.index')
            ->with('success', 'News created successfully.');
    }

    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    public function update(NewsRequest $request, News $news)
    {
        $this->newsService->updateNews(
            $news,
            $request->validated(),
            $request->file('file')
        );

        return redirect()->route('admin.news.index')
            ->with('success', 'News updated successfully.');
    }

    public function destroy(News $news)
    {
        $this->newsService->deleteNews($news);

        return redirect()->route('admin.news.index')
            ->with('success', 'News deleted successfully.');
    }
    
    public function show(Request $request, News $news)
    {
        $lang = $request->segment(1) === 'hi' ? 'hi' : 'en';

        return view('news.show', [
            'adminnews' => $news,
            'lang'      => $lang,
        ]);
    }

    public function publicIndex(Request $request)
    {
        $allNewspublic = News::latest()->get();
        $lang = $request->segment(1) === 'hi' ? 'hi' : 'en';

        return view('news.index', compact('allNewspublic', 'lang'));
    }
}
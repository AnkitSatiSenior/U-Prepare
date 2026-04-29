<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePageRequest;
use App\Http\Requests\UpdatePageRequest;
use App\Models\Page;
use App\Services\PageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class PageController extends Controller
{
    public function __construct(private readonly PageService $pages)
    {
    }

    public function showWelcomePage(?string $locale = null): View
    {
        $locale = $this->locale($locale);
        App::setLocale($locale);

        return view('welcome', $this->pages->getWelcomeData($locale));
    }

    public function showPage(string $slug): View
    {
        App::setLocale('en');

        return view('pages.show', $this->pages->getPublicPageData($slug, 'en'));
    }

    public function showPageHi(string $slug): View
    {
        App::setLocale('hi');

        return view('pages.show', $this->pages->getPublicPageData($slug, 'hi'));
    }

    public function showLocalizedPage(string $locale, string $slug): View
    {
        $locale = $this->locale($locale);
        App::setLocale($locale);

        return view('pages.show', $this->pages->getPublicPageData($slug, $locale));
    }

    public function clearCache(Request $request): JsonResponse
    {
        try {
            return response()->json([
                'status' => 'success',
                'message' => 'Cache, config, route, and view cleared successfully!',
                'output' => $this->pages->clearApplicationCaches(),
            ]);
        } catch (Throwable $e) {
            Log::error('Cache clear failed', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to clear cache.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function storageLink(Request $request): JsonResponse
    {
        try {
            return response()->json([
                'status' => 'success',
                'message' => 'Storage link created successfully!',
                'output' => $this->pages->createStorageLink(),
            ]);
        } catch (Throwable $e) {
            Log::error('Storage link failed', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create storage link.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function listPages(): View
    {
        return view('admin.pages.index', [
            'pages' => $this->pages->listPages(),
        ]);
    }

    public function showCreateForm(): View
    {
        return view('admin.pages.create');
    }

    public function createPage(StorePageRequest $request): RedirectResponse
    {
        try {
            $this->pages->createPage($request->validated());

            return redirect()
                ->route('admin.pages.list')
                ->with('success', 'Page created successfully.');
        } catch (Throwable $e) {
            Log::error('Error creating page', ['error' => $e->getMessage()]);

            return back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function showEditForm(int $id): View
    {
        return view('admin.pages.edit', [
            'page' => Page::findOrFail($id),
        ]);
    }

    public function updatePage(UpdatePageRequest $request, int $id): RedirectResponse
    {
        try {
            $this->pages->updatePage($request->validated(), $id, $request->boolean('status'));

            return redirect()
                ->route('admin.pages.list')
                ->with('success', 'Page and navigation updated successfully.');
        } catch (Throwable $e) {
            Log::error('Error updating page', ['page_id' => $id, 'error' => $e->getMessage()]);

            return back()
                ->withErrors('An unexpected error occurred.')
                ->withInput();
        }
    }

    public function deletePage(int $id): RedirectResponse
    {
        try {
            $this->pages->deletePage($id);

            return redirect()
                ->route('admin.pages.list')
                ->with('success', 'Page deleted successfully.');
        } catch (Throwable $e) {
            Log::error('Error deleting page', ['page_id' => $id, 'error' => $e->getMessage()]);

            return redirect()
                ->route('admin.pages.list')
                ->withErrors('Failed to delete the page.');
        }
    }

    private function locale(?string $locale = null): string
    {
        return $locale === 'hi' ? 'hi' : 'en';
    }
}

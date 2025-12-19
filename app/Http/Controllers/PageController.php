<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\NavbarItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Exception;

class PageController extends Controller
{
    /**
     * Clear all Laravel caches.
     */
    public function showPageHi($slug)
{
    // Set the application locale to Hindi
    App::setLocale('hi');

    // Translator instance for Hindi
    $translator = new GoogleTranslate('hi');

    // Fetch the active page by slug
    $page = Page::where('slug', $slug)
                ->where('status', 1)
                ->firstOrFail();

    // Translate or use existing Hindi content
    $body = $page->body_hindi ?: $translator->translate($page->body);
$pageTitleHI =$page->title_hi;
    // Fetch the matching navbar item
    $navbarItem = NavbarItem::where('slug', $slug)->first();

    // Initialize sidebar items
    $sidebarItems = collect();

    if ($navbarItem) {
        $parentId = $navbarItem->parent_id ?? $navbarItem->id;

        $sidebarItems = NavbarItem::where('parent_id', $parentId)
            ->where('is_active', 1)
            ->orderBy('order')
            ->get()
            ->map(function ($item) use ($translator) {
                $item->translated_title = $item->title_hi ?: $translator->translate($item->title);
                return $item;
            });
    }

    // Breadcrumb translation
    $breadcrumbs = $navbarItem ? $this->getTranslatedBreadcrumbs($navbarItem, $translator) : [];

    // Return the page view
    return view('pages.show', [
        'page'         => $page,
        'pageTitleHI'=> $pageTitleHI,
        'body'         => $body,
        'sidebarItems' => $sidebarItems,
        'breadcrumbs'  => $breadcrumbs,
        'lang'         => 'hi',
    ]);
}
    public function clearCache(Request $request)
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            return response()->json([
                'status'  => 'success',
                'message' => 'Cache, config, route, and view cleared successfully!',
                'output'  => Artisan::output(),
            ]);
        } catch (Exception $e) {
            Log::error('Cache clear failed: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to clear cache.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
   

    // Create storage symlink (useful for file uploads)
    public function storageLink(Request $request)
    {
        try {
            Artisan::call('storage:link');

            return response()->json([
                'status'  => 'success',
                'message' => 'Storage link created successfully!',
                'output'  => Artisan::output(),
            ]);
        } catch (Exception $e) {
            Log::error('Storage link failed: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to create storage link.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * List all pages (without pagination).
     */
    public function listPages()
    {
        $pages = Page::latest()->get();
        return view('admin.pages.index', compact('pages'));
    }

    /**
     * Show create form.
     */
    public function showCreateForm()
    {
        return view('admin.pages.create');
    }

    /**
     * Store a new page.
     */
    public function createPage(Request $request)
    {
        try {
            $validated = $this->validatePageRequest($request);

            $validated['slug']   = Page::createSlug($validated['title']);
            $validated['status'] = 0; // default inactive

            Page::create($validated);

            return redirect()
                ->route('admin.pages.list')
                ->with('success', 'Page created successfully.');
        } catch (Exception $e) {
            Log::error('Error creating page: ' . $e->getMessage());

            return back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Show edit form.
     */
    public function showEditForm($id)
    {
        $page = Page::findOrFail($id);
        return view('admin.pages.edit', compact('page'));
    }

    /**
     * Update an existing page.
     */
    public function updatePage(Request $request, $id)
{
    try {
        $page = Page::findOrFail($id);

        $validated = $this->validatePageRequest($request, $page->id);
        $validated['status'] = $request->boolean('status') ? 1 : 0;

        // Ensure slug is always set
        if (!isset($validated['slug']) || empty($validated['slug'])) {
            $validated['slug'] = $page->slug;
        }

        $page->update($validated);

        // Sync navbar item active state
        NavbarItem::where('slug', $validated['slug'])
            ->update(['is_active' => $validated['status']]);

        return redirect()->route('admin.pages.list')
            ->with('success', 'Page and navigation updated successfully.');
    } catch (Exception $e) {
        Log::error('Error updating page: ' . $e->getMessage());

        return back()
            ->withErrors('An unexpected error occurred.')
            ->withInput();
    }
}


    /**
     * Delete a page.
     */
    public function deletePage($id)
    {
        try {
            $page = Page::findOrFail($id);
            $page->delete();

            return redirect()
                ->route('admin.pages.list')
                ->with('success', 'Page deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error deleting page: ' . $e->getMessage());

            return redirect()
                ->route('admin.pages.list')
                ->withErrors('Failed to delete the page.');
        }
    }

    /**
     * Show a page by slug (English default).
     */
    public function showPage($slug)
    {
        $page = Page::where('slug', $slug)->where('status', 1)->firstOrFail();
        $navbarItem = NavbarItem::where('slug', $slug)->first();

        $sidebarItems = collect();
        if ($navbarItem) {
            $parentId = $navbarItem->parent_id ?? $navbarItem->id;
            $sidebarItems = NavbarItem::where('parent_id', $parentId)
                ->where('is_active', 1)
                ->orderBy('order')
                ->get();
        }

        $breadcrumbs = $navbarItem ? $this->getBreadcrumbs($navbarItem) : [];

        return view('pages.show', [
            'page'         => $page,
            'body'         => $page->body_eng,
            'sidebarItems' => $sidebarItems,
            'breadcrumbs'  => $breadcrumbs,
            'lang'         => 'en',
        ]);
    }

    /**
     * Show localized page.
     */
    public function showLocalizedPage($lang, $slug)
    {
        App::setLocale($lang);

        $page = Page::where('slug', $slug)->where('status', 1)->firstOrFail();

        $locale     = $lang === 'hi' ? 'hi' : 'en';
        $translator = $locale === 'hi' ? new GoogleTranslate('hi') : null;

        $body = $locale === 'hi'
            ? ($page->body_hindi ?: ($translator?->translate($page->body_eng)))
            : $page->body_eng;

        $navbarItem = NavbarItem::where('slug', $slug)->first();
        $sidebarItems = collect();

        if ($navbarItem) {
            $parentId = $navbarItem->parent_id ?? $navbarItem->id;

            $sidebarItems = NavbarItem::where('parent_id', $parentId)
                ->where('is_active', 1)
                ->orderBy('order')
                ->get()
                ->map(function ($item) use ($locale, $translator) {
                    $item->translated_title = $locale === 'hi' && $translator
                        ? ($item->title_hi ?: $translator->translate($item->title))
                        : $item->title;
                    return $item;
                });
        }

        $breadcrumbs = $navbarItem
            ? ($locale === 'hi' && $translator
                ? $this->getTranslatedBreadcrumbs($navbarItem, $translator)
                : $this->getBreadcrumbs($navbarItem))
            : [];

        return view('pages.show', [
            'page'         => $page,
            'body'         => $body,
            'sidebarItems' => $sidebarItems,
            'breadcrumbs'  => $breadcrumbs,
            'lang'         => $locale,
        ]);
    }

    /**
     * Welcome page (localized).
     */
    public function showWelcomePage($lang = 'en')
    {
        App::setLocale($lang);
        $translator = $lang === 'hi' ? new GoogleTranslate('hi') : null;

        $title = $lang === 'hi' && $translator
            ? $translator->translate('Welcome to our site!')
            : 'Welcome to our site!';

        $description = $lang === 'hi' && $translator
            ? $translator->translate('This is our homepage.')
            : 'This is our homepage.';

        return view('welcome', compact('title', 'description', 'lang'));
    }

    /**
     * Shared validation.
     */
    private function validatePageRequest(Request $request, $id = null)
    {
        return $request->validate([
            'title'            => 'required|string|max:255',
            'title_hi'         => 'nullable|string|max:255',
            'slug'             => 'sometimes|string|max:255|unique:pages,slug,' . $id,
            'body_eng'         => 'sometimes|string',
            'body_hindi'       => 'sometimes|string',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords'    => 'nullable|string',
        ]);
    }

    /**
     * Build breadcrumbs in English.
     */
    private function getBreadcrumbs($navbarItem)
    {
        $breadcrumbs = [];
        while ($navbarItem) {
            $breadcrumbs[] = $navbarItem;
            $navbarItem = $navbarItem->parent;
        }
        return array_reverse($breadcrumbs);
    }

    /**
     * Build translated breadcrumbs.
     */
    private function getTranslatedBreadcrumbs($navbarItem, GoogleTranslate $translator)
    {
        $breadcrumbs = [];
        while ($navbarItem) {
            array_unshift($breadcrumbs, [
                'title' => $navbarItem->title_hi ?: $translator->translate($navbarItem->title),
                'slug'  => $navbarItem->slug,
            ]);
            $navbarItem = $navbarItem->parent;
        }
        return $breadcrumbs;
    }
}

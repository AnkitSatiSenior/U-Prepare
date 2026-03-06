<?php

use Illuminate\Support\Facades\App;
use App\Models\{NavbarItem, Page, Slide, Leader, PackageComponent, Video, News};
use App\Helpers\TranslationHelper;

if (!function_exists('getNavbarItems')) {
    function getNavbarItems()
    {
        $locale = App::getLocale();

        // 1. Fetch main items
        $items = NavbarItem::with('children')
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        // 2. Gather all routes to prevent N+1 Queries
        $allRoutes = $items->pluck('route')
            ->merge($items->pluck('children.*.route')->flatten())
            ->filter()
            ->unique();

        // 3. Fetch all related pages in a SINGLE query
        $pages = Page::whereIn('slug', $allRoutes)->get()->keyBy('slug');

        // 4. Process items in-memory without hitting the database repeatedly
        $items->each(fn($item) => processNavbarItem($item, $locale, $pages));

        return $items;
    }
}

if (!function_exists('processNavbarItem')) {
    function processNavbarItem($item, string $locale, $pages): void
    {
        // Resolve page from the pre-loaded collection
        $item->page = $pages->get($item->route);
        
        // Caution: If TranslationHelper uses an external API, this will bottleneck.
        $item->translated_title = $locale === 'hi' && !empty($item->title_hi)
            ? $item->title_hi
            : TranslationHelper::translate($item->title, $locale);

        $item->children->each(fn($child) => processNavbarItem($child, $locale, $pages));
    }
}

if (!function_exists('getSlides')) {
    function getSlides()
    {
        return Slide::where('status', true)->orderBy('order')->get();
    }
}

if (!function_exists('getLeaders')) {
    function getLeaders()
    {
        return Leader::where('status', true)->orderBy('order')->get();
    }
}

if (!function_exists('getPackageComponents')) {
    function getPackageComponents()
    {
        return PackageComponent::orderBy('created_at', 'desc')->get();
    }
}

if (!function_exists('getVideos')) {
    function getVideos()
    {
        return Video::where('status', true)->orderBy('order')->get();
    }
}

if (!function_exists('getNews')) {
    function getNews()
    {
        return News::orderBy('created_at', 'desc')->get();
    }
}
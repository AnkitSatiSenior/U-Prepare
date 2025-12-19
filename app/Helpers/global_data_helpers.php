<?php

use Illuminate\Support\Facades\{Cache, App};
use App\Models\{NavbarItem, Page, Slide, Leader, PackageComponent, Video, News};
use App\Helpers\TranslationHelper;

if (!function_exists('getNavbarItems')) {
    function getNavbarItems()
    {
        $locale = App::getLocale();
        $cacheKey = "navbar_items_{$locale}";

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($locale) {
            $items = NavbarItem::with('children')
                ->whereNull('parent_id')
                ->where('is_active', true)
                ->orderBy('order')
                ->get();

            $items->each(fn($item) => processNavbarItem($item, $locale));

            return $items;
        });
    }
}

if (!function_exists('processNavbarItem')) {
    function processNavbarItem($item, string $locale): void
    {
        $item->page = Page::where('slug', $item->route)->first();
        $item->translated_title = $locale === 'hi' && !empty($item->title_hi)
            ? $item->title_hi
            : TranslationHelper::translate($item->title, $locale);

        $item->children->each(fn($child) => processNavbarItem($child, $locale));
    }
}

if (!function_exists('getSlides')) {
    function getSlides()
    {
        return Cache::remember('all_slides', now()->addMinutes(10), fn() =>
            Slide::where('status', true)->orderBy('order')->get()
        );
    }
}

if (!function_exists('getLeaders')) {
    function getLeaders()
    {
        return Cache::remember('all_leaders', now()->addMinutes(10), fn() =>
            Leader::where('status', true)->orderBy('order')->get()
        );
    }
}

if (!function_exists('getPackageComponents')) {
    function getPackageComponents()
    {
        return Cache::remember('all_package_components', now()->addMinutes(10), fn() =>
            PackageComponent::orderBy('created_at', 'desc')->get()
        );
    }
}

if (!function_exists('getVideos')) {
    function getVideos()
    {
        return Cache::remember('all_videos', now()->addMinutes(10), fn() =>
            Video::where('status', true)->orderBy('order')->get()
        );
    }
}

if (!function_exists('getNews')) {
    function getNews()
    {
        return Cache::remember('all_news', now()->addMinutes(10), fn() =>
            News::orderBy('created_at', 'desc')->get()
        );
    }
}

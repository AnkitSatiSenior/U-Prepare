<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use App\Models\{NavbarItem, Page, Slide, Leader, PackageComponent, Video, News};
use App\Helpers\TranslationHelper;

if (!function_exists('currentPublicLocale')) {
    function currentPublicLocale(?string $locale = null): string
    {
        $locale = $locale ?: request()->segment(1) ?: App::getLocale();

        return $locale === 'hi' ? 'hi' : 'en';
    }
}

if (!function_exists('localizedPageUrl')) {
    function localizedPageUrl(?string $slug, ?string $locale = null): string
    {
        if (empty($slug)) {
            return '#';
        }

        $locale = currentPublicLocale($locale);
        $exists = Page::where('slug', $slug)->where('status', 1)->exists();

        return $exists ? url($locale . '/' . $slug) : '#';
    }
}

if (!function_exists('getNavbarItems')) {
    function getNavbarItems()
    {
        $locale = currentPublicLocale();

        $items = NavbarItem::with('children')
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        $pageSlugs = collectNavbarPageSlugs($items);

        $pages = Page::whereIn('slug', $pageSlugs)
            ->where('status', 1)
            ->get()
            ->keyBy('slug');

        $items->each(fn($item) => processNavbarItem($item, $locale, $pages));

        return $items;
    }
}

if (!function_exists('collectNavbarPageSlugs')) {
    function collectNavbarPageSlugs($items)
    {
        return $items
            ->flatMap(function ($item) {
                return collect([$item->route, $item->slug])
                    ->merge($item->children ? collectNavbarPageSlugs($item->children) : collect());
            })
            ->filter()
            ->unique();
    }
}

if (!function_exists('processNavbarItem')) {
    function processNavbarItem($item, string $locale, $pages): void
    {
        $item->page = $pages->get($item->route) ?: $pages->get($item->slug);
        $item->public_link = resolveNavbarItemUrl($item, $locale, $pages);
        
        $item->translated_title = $locale === 'hi' && !empty($item->title_hi)
            ? $item->title_hi
            : TranslationHelper::translate($item->title, $locale);

        $item->children->each(fn($child) => processNavbarItem($child, $locale, $pages));
    }
}

if (!function_exists('resolveNavbarItemUrl')) {
    function resolveNavbarItemUrl($item, string $locale, $pages): string
    {
        if (!empty($item->route) && Route::has($item->route)) {
            try {
                return route($item->route);
            } catch (\Throwable $e) {
                return '#';
            }
        }

        $page = $pages->get($item->route) ?: $pages->get($item->slug);
        if ($page) {
            return url($locale . '/' . $page->slug);
        }

        if (!empty($item->url)) {
            return $item->url;
        }

        return '#';
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

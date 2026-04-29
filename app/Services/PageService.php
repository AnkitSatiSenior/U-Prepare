<?php

namespace App\Services;

use App\Models\NavbarItem;
use App\Models\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PageService
{
    private const CACHE_TTL = 3600;

    public function getPublicPageData(string $slug, string $locale): array
    {
        $locale = $this->normalizeLocale($locale);
        $cacheKey = "public_page:{$locale}:{$slug}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($slug, $locale) {
            $page = Page::query()
                ->where('slug', $slug)
                ->where('status', true)
                ->firstOrFail();

            $navbarItems = $this->activeNavbarItems();
            $navbarItem = $navbarItems->first(
                fn (NavbarItem $item) => $item->slug === $slug || $item->route === $slug
            );

            return [
                'page' => $page,
                'body' => $this->localizedBody($page, $locale),
                'sidebarItems' => $this->sidebarItems($navbarItems, $navbarItem, $locale),
                'breadcrumbs' => $this->breadcrumbs($navbarItems, $navbarItem, $locale),
                'lang' => $locale,
            ];
        });
    }

    public function getWelcomeData(string $locale): array
    {
        $locale = $this->normalizeLocale($locale);

        return Cache::remember("public_home:{$locale}", self::CACHE_TTL, fn () => [
            'title' => $locale === 'hi' ? 'यू-प्रिपेयर में आपका स्वागत है' : 'Welcome to U-PREPARE',
            'description' => $locale === 'hi'
                ? 'उत्तराखंड आपदा तैयारी और लचीलापन परियोजना'
                : 'Uttarakhand Disaster Preparedness and Resilience Project',
            'lang' => $locale,
        ]);
    }

    public function listPages(): Collection
    {
        return Cache::remember('admin_pages:list', 600, fn () => Page::latest()->get());
    }

    public function createPage(array $validated): Page
    {
        $validated['slug'] = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Page::createSlug($validated['title']);
        $validated['status'] = false;

        $page = Page::create($validated);
        $this->forgetPageCaches();

        return $page;
    }

    public function updatePage(array $validated, int $id, bool $status): Page
    {
        return DB::transaction(function () use ($validated, $id, $status) {
            $page = Page::findOrFail($id);
            $validated['slug'] = !empty($validated['slug']) ? Str::slug($validated['slug']) : $page->slug;
            $validated['status'] = $status;

            $page->update($validated);

            NavbarItem::where('slug', $page->slug)
                ->orWhere('slug', $validated['slug'])
                ->update(['is_active' => $validated['status']]);

            $this->forgetPageCaches();

            return $page->refresh();
        });
    }

    public function deletePage(int $id): void
    {
        Page::findOrFail($id)->delete();
        $this->forgetPageCaches();
    }

    public function clearApplicationCaches(): string
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');

        return Artisan::output();
    }

    public function createStorageLink(): string
    {
        Artisan::call('storage:link');

        return Artisan::output();
    }

    public function forgetPageCaches(): void
    {
        Cache::forget('admin_pages:list');

        Page::query()
            ->select('slug')
            ->whereNotNull('slug')
            ->get()
            ->each(function (Page $page) {
                Cache::forget("public_page:en:{$page->slug}");
                Cache::forget("public_page:hi:{$page->slug}");
            });

        Cache::forget('public_home:en');
        Cache::forget('public_home:hi');
        Cache::forget('navbar:active_items');
    }

    private function activeNavbarItems(): Collection
    {
        return Cache::remember('navbar:active_items', self::CACHE_TTL, fn () => NavbarItem::query()
            ->where('is_active', true)
            ->orderBy('order')
            ->get());
    }

    private function sidebarItems(Collection $items, ?NavbarItem $navbarItem, string $locale): Collection
    {
        if (!$navbarItem) {
            return collect();
        }

        $parentId = $navbarItem->parent_id ?: $navbarItem->id;

        return $items
            ->where('parent_id', $parentId)
            ->sortBy('order')
            ->values()
            ->map(fn (NavbarItem $item) => $this->withLocalizedTitle($item, $locale));
    }

    private function breadcrumbs(Collection $items, ?NavbarItem $navbarItem, string $locale): array
    {
        if (!$navbarItem) {
            return [];
        }

        $byId = $items->keyBy('id');
        $breadcrumbs = [];
        $current = $navbarItem;

        while ($current) {
            array_unshift($breadcrumbs, $this->withLocalizedTitle($current, $locale));
            $current = $current->parent_id ? $byId->get($current->parent_id) : null;
        }

        return $breadcrumbs;
    }

    private function withLocalizedTitle(NavbarItem $item, string $locale): NavbarItem
    {
        $item->translated_title = $locale === 'hi' && !empty($item->title_hi)
            ? $item->title_hi
            : $item->title;

        return $item;
    }

    private function localizedBody(Page $page, string $locale): string
    {
        return $locale === 'hi'
            ? (string) ($page->body_hindi ?: $page->body_eng)
            : (string) $page->body_eng;
    }

    private function normalizeLocale(?string $locale): string
    {
        return $locale === 'hi' ? 'hi' : 'en';
    }

}

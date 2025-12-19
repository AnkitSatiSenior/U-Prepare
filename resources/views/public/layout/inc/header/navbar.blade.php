@php
    $locale = app()->getLocale();
    $localePrefix = $locale === 'hi' ? 'hi' : 'en';
    $currentSlug = request()->segment(2) ?? request()->segment(1);
    $pageTitle = $page->translated_title ?? $page->title ?? 'Home';
    $navbarItems = getNavbarItems();
@endphp

<nav class="navbar">
    <div class="container-xxl">
        <ul>
            {{-- Home --}}
            <li>
                <a href="{{ route('welcome.default') }}" 
                   @class(['active' => Route::currentRouteName() === 'welcome.default'])>
                    {{ __('HOME') }}
                </a>
            </li>

            {{-- Navbar Items --}}
            @foreach ($navbarItems as $item)
                @php
                    $itemTitle = $locale === 'hi' ? ($item['title_hi'] ?? $item['title']) : $item['title'];
                    $itemUrl = $item['link'] ?? '#'; // ✅ use accessor from model
                    $hasChildren = !empty($item['children']);
                    $isDropdown = $item['is_dropdown'] ?? false;
                @endphp

                <li @class(['dropdown' => $isDropdown && $hasChildren])>
                    <a href="{{ $itemUrl }}" target="{{ $item['target'] ?? '_self' }}">
                        {{ $itemTitle }}
                        {{-- Show chevron only if dropdown + children --}}
                        @if($isDropdown && $hasChildren)
                            <i class="bi bi-chevron-down"></i>
                        @endif
                    </a>

                    {{-- Render submenu only if dropdown + children --}}
                    @if ($isDropdown && $hasChildren)
                        <ul>
                            @foreach ($item['children'] as $child)
                                @php
                                    $childTitle = $locale === 'hi' 
                                        ? ($child['translated_title'] ?? $child['title']) 
                                        : $child['title'];
                                    $childUrl = $child['link'] ?? '#';
                                @endphp
                                <li>
                                    <a href="{{ $childUrl }}" target="{{ $child['target'] ?? '_self' }}">
                                        {{ $childTitle }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach

            {{-- Login/Dashboard --}}
            <li>
                @guest
                    <a href="{{ route('login') }}">{{ __('MIS LOGIN') }}</a>
                @else
                    <a href="{{ route('dashboard') }}">{{ __('DASHBOARD') }}</a>
                @endguest
            </li>
        </ul>
    </div>
    <i class="bi bi-list mobile-nav-toggle"></i>
</nav>

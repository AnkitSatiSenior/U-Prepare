@php
    $locale = currentPublicLocale();
    $localePrefix = $locale === 'hi' ? 'hi' : 'en';
    $currentSlug = request()->segment(2) ?? request()->segment(1);
    $pageTitle = $page->translated_title ?? $page->title ?? 'Home';
    $navbarItems = getNavbarItems();
@endphp

<nav class="navbar site-nav" aria-label="{{ __('Primary navigation') }}">
    <div class="container-xxl site-nav__inner">
        <ul class="site-nav__list">
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
                    $hasChildren = !empty($item['children']);
                    $isDropdown = $item['is_dropdown'] ?? false;
                    $opensDropdown = $isDropdown && $hasChildren;
                    $itemUrl = $opensDropdown ? '#' : ($item->public_link ?? '#');
                @endphp

                <li @class(['dropdown' => $opensDropdown])>
                    <a href="{{ $itemUrl }}"
                       target="{{ $item['target'] ?? '_self' }}"
                       @if($opensDropdown) aria-haspopup="true" aria-expanded="false" @endif>
                        {{ $itemTitle }}
                        @if($opensDropdown)
                            <i class="bi bi-chevron-down" aria-hidden="true"></i>
                        @endif
                    </a>

                    @if ($opensDropdown)
                        <ul aria-label="{{ $itemTitle }}">
                            @foreach ($item['children'] as $child)
                                @php
                                    $childTitle = $locale === 'hi' 
                                        ? ($child['translated_title'] ?? $child['title']) 
                                        : $child['title'];
                                    $childUrl = $child->public_link ?? '#';
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
    <button class="bi bi-list mobile-nav-toggle" type="button" aria-label="{{ __('Open menu') }}" aria-expanded="false"></button>
</nav>

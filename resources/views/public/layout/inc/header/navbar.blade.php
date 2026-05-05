@php
    $locale = currentPublicLocale();
    $navbarItems = getNavbarItems();
@endphp

<nav class="navbar site-nav">
    <div class="container-xxl site-nav__inner">

        {{-- Mobile Toggle --}}
        <button class="bi bi-list mobile-nav-toggle" type="button"></button>

        <ul class="site-nav__list" id="navList">

            {{-- Home --}}
            <li>
                <a href="{{ route('welcome.default') }}">
                    {{ __('Home') }}
                </a>
            </li>

            @foreach ($navbarItems as $item)
                @php
                    $itemTitle = $locale === 'hi' 
                        ? ($item['title_hi'] ?? $item['title']) 
                        : $item['title'];

                    $itemTitle = \Illuminate\Support\Str::title($itemTitle);

                    $hasChildren = !empty($item['children']);
                    $isDropdown = ($item['is_dropdown'] ?? false) && $hasChildren;
                    $itemUrl = $item['link'] ?? '#';
                @endphp

                <li class="{{ $isDropdown ? 'dropdown' : '' }}">
                    <a href="{{ $isDropdown ? '#' : $itemUrl }}" 
                       class="{{ $isDropdown ? 'dropdown-toggle' : '' }}">
                        {{ $itemTitle }}
                        @if($isDropdown)
                           
                        @endif
                    </a>

                    @if ($isDropdown)
                        <ul class="dropdown-menu">
                            @foreach ($item['children'] as $child)
                                @php
                                    $childTitle = $locale === 'hi' 
                                        ? ($child['translated_title'] ?? $child['title']) 
                                        : $child['title'];

                                    $childTitle = \Illuminate\Support\Str::title($childTitle);
                                @endphp

                                <li>
                                    <a href="{{ $child['link'] ?? '#' }}">
                                        {{ $childTitle }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
            <li>
                <a href="https://drive.google.com/drive/u/0/folders/12_4FDgaJwIEh0Y3FGbz1bdErdhtmt7dv" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   title="{{ __('Download Mobile Application') }}">
                    {{ $locale === 'hi' ? 'ऐप डाउनलोड करें' : 'Download App' }}
                </a>
            </li>
            <li>
                @guest
                    <a href="{{ route('login') }}">{{ __('Mis Login') }}</a>
                @else
                    <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
                @endguest
            </li>

        </ul>
    </div>
</nav>
<style>
    .site-nav__inner {
    display: flex;
    justify-content: space-between;
    align-items: left;
}

.site-nav__list {
    display: flex;
    gap: 1.5rem;
    list-style: none;
    align-items: left !important;
}

/* Dropdown base */
.dropdown {
    position: relative;
}

.dropdown-menu {
    position: absolute;
    top: 120%;
    left: 0;
    min-width: 200px;
    background: #fff;
    display: none;
    flex-direction: column;
    padding: 10px 0;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    border-radius: 8px;
    transition: all 0.3s ease;
}

/* 🔥 HOVER (DESKTOP) */
@media (min-width: 769px) {
    .dropdown:hover > .dropdown-menu {
        display: flex;
        top: 100%;
        opacity: 1;
    }
}

/* MOBILE */
.mobile-nav-toggle {
    display: none;
    font-size: 24px;
    cursor: pointer;
}

@media (max-width: 768px) {

    .mobile-nav-toggle {
        display: block;
    }

    .site-nav__list {
        display: none;
        flex-direction: column;
        width: 100%;
        background: #fff;
        position: absolute;
        top: 100%;
        left: 0;
        padding: 1rem;
    }

    .site-nav__list.active {
        display: flex;
    }

    .dropdown-menu {
        position: static;
        display: none;
        box-shadow: none;
    }

    /* 🔥 CLICK OPEN */
    .dropdown.active > .dropdown-menu {
        display: flex;
    }
}</style>
<script>

    document.addEventListener('DOMContentLoaded', () => {

    const toggle = document.querySelector('.mobile-nav-toggle');
    const nav = document.querySelector('.site-nav__list');

    // Mobile menu toggle
    toggle.addEventListener('click', () => {
        nav.classList.toggle('active');
        toggle.classList.toggle('bi-x');
    });

    // Dropdown click (mobile only)
    document.querySelectorAll('.dropdown > a').forEach(link => {
        link.addEventListener('click', function(e) {

            if (window.innerWidth <= 768) {
                e.preventDefault();

                const parent = this.parentElement;

                // Close others
                document.querySelectorAll('.dropdown').forEach(el => {
                    if (el !== parent) el.classList.remove('active');
                });

                parent.classList.toggle('active');
            }
        });
    });

});</script>
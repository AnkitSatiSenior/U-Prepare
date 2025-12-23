@php
    $currentLang = request()->segment(1) ?? 'en';
    $segments = request()->segments();

    // Build HI URL
    $hiSegments = $segments;
    $hiSegments[0] = 'hi';
    $hiUrl = url(implode('/', $hiSegments));

    // Build EN URL
    $enSegments = $segments;
    $enSegments[0] = 'en';
    $enUrl = url(implode('/', $enSegments));
@endphp

<!-- ======= Top Bar ======== -->
<section class="container-fluid d-flex align-items-center justify-content-between top-bar py-2">
    <div class="left">
        <ul class="social-icons d-flex align-items-center list-unstyled m-0">
            <li>
                <a href="https://www.facebook.com/uprepare" target="_blank" rel="noopener">
                    <i class="bi bi-facebook"></i>
                </a>
            </li>
            <li>
                <a href="https://www.instagram.com/uprepare/" target="_blank" rel="noopener">
                    <i class="bi bi-instagram"></i>
                </a>
            </li>
            <li>
                <a href="https://x.com/prepare2344" target="_blank" rel="noopener">
                    <i class="bi bi-twitter-x"></i>
                </a>
            </li>
        </ul>
    </div>

    <div class="right d-flex align-items-center">
        <div class="lang-zoom d-flex justify-content-between">
            {{-- Language Switch --}}
            <span class="lang me-3">
                <i class="bi bi-translate"></i>
                <span class="ltyp">
                    <a href="{{ $hiUrl }}" 
                       class="{{ $currentLang === 'hi' ? 'font-bold underline text-black' : 'text-gray-600 hover:underline' }}">
                        हिन्दी
                    </a> | 
                    <a href="{{ $enUrl }}" 
                       class="{{ $currentLang === 'en' ? 'font-bold underline text-black' : 'text-gray-600 hover:underline' }}">
                        ENG
                    </a>
                </span>
            </span>

            {{-- Zoom Controls --}}
            <span class="zoom d-flex align-items-center">
                <a href="#" class="dec me-2">A-</a>
                <a href="#" class="inc">A+</a>
            </span>
        </div>
    </div>
</section>

@php
    $locale = currentPublicLocale();
    $footerGroups = [
        [
            'title' => 'EXPLORE',
            'links' => [
                ['label' => 'Who we are', 'href' => localizedPageUrl('about-us', $locale)],
                ['label' => 'Our Impact', 'href' => localizedPageUrl('objectives', $locale)],
            ],
        ],
        [
            'title' => 'QUICK ACCESS',
            'links' => [
                ['label' => 'Past Projects', 'href' => localizedPageUrl('past-projects', $locale)],
                ['label' => 'Project structure', 'href' => localizedPageUrl('project-structure', $locale)],
                ['label' => 'Tenders', 'href' => route('tender.index.public', ['locale' => $locale])],
                ['label' => 'Grievance', 'href' => route('grievances.create', ['locale' => $locale])],
            ],
        ],
    ];
    $termsUrl = localizedPageUrl('terms-and-policy', $locale);
@endphp

<footer class="site-footer">
    <div class="footer-content">
        <div class="guest-container">
            <div class="site-footer__grid">
                <div class="site-footer__brand" aria-label="{{ config('app.name') }}">
                    <img src="{{ asset('assets/img/updated-logo.png') }}" alt="{{ config('app.name') }}">
                    <p class="mb-0">
                        Uttarakhand Disaster Preparedness and Resilience Project
                    </p>
                </div>

                @foreach($footerGroups as $group)
                    <nav class="site-footer__nav" aria-label="{{ $group['title'] }}">
                        <h2>{{ $group['title'] }}</h2>
                        <ul>
                            @foreach($group['links'] as $link)
                                <li>
                                    <a href="{{ $link['href'] }}">{{ $link['label'] }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </nav>
                @endforeach
            </div>
        </div>
    </div>

    <div class="copyright-content">
        <div class="guest-container site-footer__bottom">
            <span>{{ config('app.name') }} © {{ date('Y') }}. All rights reserved.</span>
            <a href="{{ $termsUrl }}">Terms and Policy</a>
        </div>
    </div>
</footer>

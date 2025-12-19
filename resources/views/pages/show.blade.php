@php
    $locale = app()->getLocale();
    $title = $locale === 'hi' ? $page->title_hi ?? $page->title : $page->title;
    $body = $locale === 'hi' ? $page->body_hindi ?? $page->body_eng : $page->body_eng;
@endphp

<x-guest-layout>
    @section('page_title', $page->meta_title ?? $title)

    <div class="container my-2 py-3 overflow-x-auto">
        {{-- Page Header --}}
        <div class="mb-4 text-center">
            <h1 class="fw-bold">{{ $title }}</h1>
            @if ($page->meta_description)
                <p class="text-muted">{{ $page->meta_description }}</p>
            @endif
        </div>
        {{-- Page Header End --}}
        {{-- Page Body --}}
        <div class="page-body">
            {!! $body !!}
        </div>
        {{-- Page Body End --}}
    </div>
</x-guest-layout>

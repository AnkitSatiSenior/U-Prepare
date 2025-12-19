<x-guest-layout>
    @section('page_title', $lang === 'hi' ? $adminnews->title_hi : $adminnews->title_en)

    <div class="container my-2 py-3 overflow-x-auto">
        {{-- Title --}}
        <h1 class="mb-3">
            {!! $lang === 'hi' ? $adminnews->title_hi : $adminnews->title_en !!}
        </h1>

        {{-- Body Content --}}
        <div class="mb-4">
            {!! $lang === 'hi' ? $adminnews->body_hi : $adminnews->body_en !!}
        </div>

        {{-- Attachment --}}
        @if($adminnews->file)
            <p>
                <a href="{{ Storage::url($adminnews->file) }}" target="_blank" class="btn btn-primary">
                    {!! $lang === 'hi' ? 'संलग्न देखें' : 'View Attachment' !!}
                </a>
            </p>
        @endif
    </div>
</x-guest-layout>

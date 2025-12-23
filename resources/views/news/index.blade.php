<x-guest-layout>
    @section('page_title', 'News')
    <div class="container py-4">

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-light p-2 rounded">
                <li class="breadcrumb-item">
                    <a href="{{ url('/') }}">
                        {!! $lang === 'hi' ? 'होम' : 'Home' !!}
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    {!! $lang === 'hi' ? 'समाचार' : 'News' !!}
                </li>
            </ol>
        </nav>

        <div class="row">

            <!-- Main content -->
            <div class="col-lg-8">
                <h2 class="mb-4">{!! $lang === 'hi' ? 'समाचार' : 'News' !!}</h2>

                @forelse($allNewspublic as $adminnews)
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="{{ route('news.show', $adminnews->id) }}">
                                    {!! $lang === 'hi' ? $adminnews->title_hi : $adminnews->title_en !!}
                                </a>
                            </h5>
                            <p class="text-muted mb-2">{{ $adminnews->created_at->format('d M Y') }}</p>
                            <p class="card-text">
                                {!! \Illuminate\Support\Str::limit(
                                    $lang === 'hi' ? $adminnews->body_hi : $adminnews->body_en,
                                    200
                                ) !!}
                            </p>
                            <a href="{{ route('news.show', $adminnews->id) }}" class="btn btn-sm btn-primary">
                                {!! $lang === 'hi' ? 'पूरा पढ़ें' : 'Read More' !!}
                            </a>
                        </div>
                    </div>
                @empty
                    <p>{!! $lang === 'hi' ? 'कोई समाचार उपलब्ध नहीं है।' : 'No news available.' !!}</p>
                @endforelse
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        {!! $lang === 'hi' ? 'सभी समाचार' : 'All News' !!}
                    </div>
                    <ul class="list-group list-group-flush">
                        @forelse($allNewspublic as $adminnews)
                            <li class="list-group-item">
                                <a href="{{ route('news.show', $adminnews->id) }}">
                                    {!! $lang === 'hi' ? $adminnews->title_hi : $adminnews->title_en !!}
                                </a>
                            </li>
                        @empty
                            <li class="list-group-item">
                                {!! $lang === 'hi' ? 'कोई समाचार उपलब्ध नहीं है।' : 'No news available.' !!}
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>

        </div>
    </div>
</x-guest-layout>

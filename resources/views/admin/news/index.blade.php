<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header
            icon="fas fa-newspaper text-primary"
            title="News Management"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'News']
            ]"
        />

        <!-- Alerts -->
        @if (session('success'))
            <div class="row mb-3">
                <div class="col-md-12">
                    <x-alert type="success" :message="session('success')" dismissible />
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="row mb-3">
                <div class="col-md-12">
                    <x-alert type="danger" :message="session('error')" dismissible />
                </div>
            </div>
        @endif

        <!-- News Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-list me-2"></i> News List
                </h5>
                <a href="{{ route('admin.news.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Add News
                </a>
            </div>

            <div class="card-body">
                <x-admin.data-table 
                    id="news-table"
                    :headers="[
                        'ID', 'Title (EN)', 'Title (HI)', 'File', 'Created At', 'Actions'
                    ]"
                    :excel="true"
                    :print="true"
                    title="News Export"
                    searchPlaceholder="Search news..."
                    resourceName="news"
                    :pageLength="10"
                >
                    @foreach ($news as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->title_en }}</td>
                            <td>{{ $item->title_hi }}</td>
                            <td>
                                @if($item->file)
                                    <a href="{{ asset('storage/'.$item->file) }}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-file-alt me-1"></i> View
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $item->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.news.edit', $item->id) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.news.destroy', $item->id) }}" 
                                          method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this news?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash-alt me-1"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-admin.data-table>
            </div>
        </div>
    </div>
</x-app-layout>

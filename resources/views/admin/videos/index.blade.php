<x-app-layout>
    <div class="container-fluid">
        <x-admin.breadcrumb-header 
            icon="fas fa-video text-primary" 
            title="Videos Management"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Videos']
            ]"  
        />

        @if(session('success'))
            <x-alert type="success" :message="session('success')" dismissible />
        @endif

        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-list me-2"></i> Video List
                </h5>
                <a href="{{ route('admin.videos.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Add Video
                </a>
            </div>

            <div class="card-body">
                <x-admin.data-table 
                    id="videos-table"
                    :headers="['ID','Thumbnail','Text','Link','Status','Order','Actions']"
                    :excel="true"
                    :print="true"
                    title="Videos Export"
                    searchPlaceholder="Search videos..."
                    resourceName="videos"
                    :pageLength="10"
                >
                    @foreach ($adminVideos as $video)
                        <tr>
                            <td>{{ $video->id }}</td>
                            <td>
                                @if($video->img)
                                    <img src="{{ asset('storage/'.$video->img) }}" width="80" class="rounded shadow">
                                @else
                                    <span class="text-muted">No Image</span>
                                @endif
                            </td>
                            <td>{{ $video->text }}</td>
                            <td><a href="{{ $video->link }}" target="_blank">View</a></td>
                            <td>
                                <span class="badge {{ $video->status ? 'bg-success' : 'bg-danger' }}">
                                    {{ $video->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>{{ $video->order }}</td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.videos.edit', $video) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.videos.destroy', $video) }}" method="POST" 
                                        onsubmit="return confirm('Delete this video?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash-alt"></i>
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

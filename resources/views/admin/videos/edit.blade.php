<x-app-layout>
    <div class="container">
        <x-admin.breadcrumb-header 
            icon="fas fa-video text-primary" 
            title="Edit Video"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['route' => 'admin.videos.index', 'label' => 'Videos'],
                ['label' => 'Edit']
            ]"  
        />

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('admin.videos.update', $video) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Text</label>
                        <input type="text" name="text" value="{{ $video->text }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Link</label>
                        <input type="url" name="link" value="{{ $video->link }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Thumbnail</label><br>
                        @if($video->img)
                            <img src="{{ asset('storage/'.$video->img) }}" width="120" class="mb-2 rounded shadow"><br>
                        @endif
                        <input type="file" name="img" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="1" {{ $video->status ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ !$video->status ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Order</label>
                        <input type="number" name="order" value="{{ $video->order }}" class="form-control">
                    </div>
                    <button class="btn btn-primary">Update</button>
                    <a href="{{ route('admin.videos.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

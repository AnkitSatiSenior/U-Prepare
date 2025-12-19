<x-app-layout>
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header
            icon="fas fa-file-contract text-primary"
            title="Edit Tender"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['route' => 'admin.tenders.index', 'label' => 'Tenders'],
                ['label' => 'Edit']
            ]"
        />

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('admin.tenders.update', $tender) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Title (EN)</label>
                        <input type="text" name="title_en" class="form-control" value="{{ old('title_en', $tender->title_en) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Title (HI)</label>
                        <input type="text" name="title_hi" class="form-control" value="{{ old('title_hi', $tender->title_hi) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description (EN)</label>
                        <textarea name="description_en" class="form-control">{{ old('description_en', $tender->description_en) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description (HI)</label>
                        <textarea name="description_hi" class="form-control">{{ old('description_hi', $tender->description_hi) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Open Date</label>
                        <input type="date" name="open_date" class="form-control" value="{{ old('open_date', $tender->open_date->format('Y-m-d')) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Close Date</label>
                        <input type="date" name="close_date" class="form-control" value="{{ old('close_date', $tender->close_date->format('Y-m-d')) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Current File</label>
                        @if($tender->file)
                            <p><a href="{{ Storage::url($tender->file) }}" target="_blank">View File</a></p>
                        @else
                            <p>No file uploaded</p>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Change File</label>
                        <input type="file" name="file" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary">Update Tender</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

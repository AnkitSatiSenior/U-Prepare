{{-- resources/views/admin/package_components/create.blade.php --}}
<x-app-layout>
    <div class="container-fluid">
        <x-admin.breadcrumb-header 
            icon="fas fa-plus-circle text-primary" 
            title="Create Package Component" 
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'], 
                ['label' => 'Admin'],
                ['route' => 'admin.package-components.index', 'label' => 'Package Components'],
                ['label' => 'Create']
            ]"  
        /> 

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('admin.package-components.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Name --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            name="name" 
                            class="form-control @error('name') is-invalid @enderror" 
                            value="{{ old('name') }}" 
                            required
                        >
                        @error('name') 
                            <div class="invalid-feedback">{{ $message }}</div> 
                        @enderror
                    </div>

                    {{-- Budget --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Budget (₹)</label>
                        <input 
                            type="number" 
                            step="0.01" 
                            name="budget" 
                            class="form-control @error('budget') is-invalid @enderror" 
                            value="{{ old('budget') }}"
                        >
                        @error('budget') 
                            <div class="invalid-feedback">{{ $message }}</div> 
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea 
                            name="description" 
                            id="description" 
                            rows="4" 
                            class="form-control @error('description') is-invalid @enderror"
                        >{{ old('description') }}</textarea>
                        @error('description') 
                            <div class="invalid-feedback">{{ $message }}</div> 
                        @enderror
                    </div>

                    {{-- Image --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Image</label>
                        <input 
                            type="file" 
                            name="image" 
                            class="form-control @error('image') is-invalid @enderror"
                        >
                        @error('image') 
                            <div class="invalid-feedback">{{ $message }}</div> 
                        @enderror
                    </div>

                    {{-- Hindi Title --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Page Hindi Title</label>
                        <input 
                            type="text" 
                            name="page_hin_title" 
                            class="form-control @error('page_hin_title') is-invalid @enderror" 
                            value="{{ old('page_hin_title') }}"
                        >
                        @error('page_hin_title') 
                            <div class="invalid-feedback">{{ $message }}</div> 
                        @enderror
                    </div>

                    {{-- English Title --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Page English Title</label>
                        <input 
                            type="text" 
                            name="page_eng_title" 
                            class="form-control @error('page_eng_title') is-invalid @enderror" 
                            value="{{ old('page_eng_title') }}"
                        >
                        @error('page_eng_title') 
                            <div class="invalid-feedback">{{ $message }}</div> 
                        @enderror
                    </div>

                    {{-- Hindi Content --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Hindi Content</label>
                        <textarea 
                            name="hin_content" 
                            rows="4" 
                            class="form-control @error('hin_content') is-invalid @enderror"
                        >{{ old('hin_content') }}</textarea>
                        @error('hin_content') 
                            <div class="invalid-feedback">{{ $message }}</div> 
                        @enderror
                    </div>

                    {{-- English Content --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">English Content</label>
                        <textarea 
                            name="eng_content" 
                            rows="4" 
                            class="form-control @error('eng_content') is-invalid @enderror"
                        >{{ old('eng_content') }}</textarea>
                        @error('eng_content') 
                            <div class="invalid-feedback">{{ $message }}</div> 
                        @enderror
                    </div>

                    {{-- Actions --}}
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save
                        </button>
                        <a href="{{ route('admin.package-components.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

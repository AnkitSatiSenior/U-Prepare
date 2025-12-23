<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header
            icon="fas fa-images text-primary"
            title="Slides Management"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Slides']
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

        <!-- Slides Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-list me-2"></i> Slides List
                </h5>
                <a href="{{ route('admin.slides.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Add Slide
                </a>
            </div>

            <div class="card-body">
                <x-admin.data-table 
                    id="slides-table"
                    :headers="['ID','Image','Head','Subh','Order','Status','Actions']"
                    :excel="true"
                    :print="true"
                    title="Slides Export"
                    searchPlaceholder="Search slides..."
                    resourceName="slides"
                    :pageLength="10"
                >
                    @foreach ($adminslides as $slide)
                        <tr>
                            <td>{{ $slide->id }}</td>
                           <td>
    <img src="{{ asset('storage/app/public/' . $slide->img) }}" 
         alt="slide image" 
         class="rounded shadow-sm" 
         width="100">
</td>

                            <td>{{ $slide->head }}</td>
                            <td>{{ $slide->subh }}</td>
                            <td>{{ $slide->order }}</td>
                            <td>
                                <span class="badge {{ $slide->status ? 'bg-success' : 'bg-danger' }}">
                                    {{ $slide->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.slides.edit', $slide) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.slides.destroy', $slide) }}" 
                                          method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this slide?')">
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

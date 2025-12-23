<x-app-layout>
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header
            icon="fas fa-plus-circle text-primary"
            title="Create Safeguard Compliance"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['route' => 'admin.safeguard-compliances.index', 'label' => 'Safeguard Compliances'],
                ['label' => 'Create']
            ]"
        />

        <!-- Form -->
        <div class="card shadow-sm mt-3">
            <div class="card-body">
                <form action="{{ route('admin.safeguard-compliances.store') }}" method="POST">
                    @csrf

                    <!-- Compliance Name -->
                    <div class="mb-3">
                        <label class="form-label">Compliance Name</label>
                        <input type="text" 
                               name="name" 
                               value="{{ old('name') }}" 
                               class="form-control" 
                               required>
                        @error('name') 
                            <div class="text-danger small">{{ $message }}</div> 
                        @enderror
                    </div>

                    <!-- Allowed Role -->
                    <div class="mb-3">
                        <label class="form-label">Allowed Role</label>
                        <select name="role_id" class="form-control" required>
                            <option value="">-- Select Role --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" 
                                    {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id') 
                            <div class="text-danger small">{{ $message }}</div> 
                        @enderror
                    </div>

                    <!-- Contraction Phases Checklist -->
                    <div class="mb-3">
                        <label class="form-label">Contraction Phases</label>
                        <div class="row">
                            @foreach($phases as $phase)
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="contraction_phase_ids[]" 
                                               value="{{ $phase->id }}"
                                               id="phase-{{ $phase->id }}"
                                               {{ in_array($phase->id, old('contraction_phase_ids', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="phase-{{ $phase->id }}">
                                            {{ $phase->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('contraction_phase_ids') 
                            <div class="text-danger small">{{ $message }}</div> 
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <a href="{{ route('admin.safeguard-compliances.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header icon="fas fa-shield-alt text-info" title="Add Safeguard Entry" :breadcrumbs="[
            ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
            ['route' => 'admin.safeguard_entries.index', 'label' => '<i class=\'fas fa-shield-alt\'></i> Safeguard Entries'],
            ['class' => 'active', 'label' => 'Create'],
        ]" />

        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-plus-circle me-2"></i> Safeguard Entry Details
                </h5>
            </div>

            <div class="card-body">
                <form action="{{ route('admin.safeguard_entries.store') }}" method="POST">
                    @csrf

                    <div class="row mb-3">
                        <!-- Sub Package Project -->
                        <div class="col-md-4">
                            <x-label for="sub_package_project_id" :value="'Sub Package Project'" />
                            <x-bootstrap.dropdown
                                id="sub_package_project_id"
                                name="sub_package_project_id"
                                :items="$projects->map(fn($p) => ['value' => $p->id, 'label' => $p->name])->toArray()"
                                :selected="old('sub_package_project_id')"
                                placeholder="Select Project"
                                required
                            />
                            @error('sub_package_project_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Safeguard Compliance -->
                        <div class="col-md-4">
                            <x-label for="safeguard_compliance_id" :value="'Safeguard Compliance'" />
                            <x-bootstrap.dropdown
                                id="safeguard_compliance_id"
                                name="safeguard_compliance_id"
                                :items="$compliances->map(fn($c) => ['value' => $c->id, 'label' => $c->name])->toArray()"
                                :selected="old('safeguard_compliance_id')"
                                placeholder="Select Compliance"
                                required
                            />
                            @error('safeguard_compliance_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Construction Phase -->
                        <div class="col-md-4">
                            <x-label for="contraction_phase_id" :value="'Construction Phase'" />
                            <x-bootstrap.dropdown
                                id="contraction_phase_id"
                                name="contraction_phase_id"
                                :items="$phases->map(fn($p) => ['value' => $p->id, 'label' => $p->name])->toArray()"
                                :selected="old('contraction_phase_id')"
                                placeholder="Select Phase"
                                required
                            />
                            @error('contraction_phase_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <!-- SL No -->
                        <div class="col-md-4">
                            <x-label for="sl_no" :value="'SL No'" />
                            <x-input type="text" name="sl_no" id="sl_no" value="{{ old('sl_no') }}" />
                            @error('sl_no')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Item Description -->
                        <div class="col-md-8">
                            <x-label for="item_description" :value="'Item Description'" />
                            <textarea name="item_description" id="item_description">{{ old('item_description') }}</textarea>
                            @error('item_description')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-check mt-4">
                                <x-input type="checkbox" name="is_validity" id="is_validity" value="1" :checked="old('is_validity')" class="form-check-input" />
                                <x-label for="is_validity" :value="'Is Validity for This Test?'" class="form-check-label" />
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin.safeguard_entries.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i> Save
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>

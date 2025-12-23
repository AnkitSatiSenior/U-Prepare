<x-app-layout>
    <div class="container-fluid py-4">
        <!-- Header + Breadcrumb -->
       <x-admin.breadcrumb-header
    icon="fas fa-users text-primary"
    title="{{ isset($user) ? 'Edit User' : 'Create User' }}"
    :breadcrumbs="[
        ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
        ['label' => 'Admin'],
        ['route' => 'admin.users.index', 'label' => 'Users'],
        ['label' => isset($user) ? 'Edit' : 'Create']
    ]"
/>

        <!-- Form Card -->
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom-0">
                <h5 class="mb-0 text-primary fw-bold">
                    <i class="fas {{ isset($user) ? 'fa-edit' : 'fa-user-plus' }} me-2"></i>
                    {{ isset($user) ? 'Edit User Details' : 'Create New User' }}
                </h5>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>

            <div class="card-body">
                <form action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}"
                      method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    @if (isset($user))
                        @method('PUT')
                    @endif

                    <div class="row g-3">
                        {{-- Full Name --}}
                        <div class="col-md-4">
                            <x-label for="name" value="Full Name" required />
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <x-input id="name" name="name" value="{{ old('name', $user->name ?? '') }}" required />
                            </div>
                            <x-input-error for="name" />
                        </div>

                        {{-- Email --}}
                        <div class="col-md-4">
                            <x-label for="email" value="Email" required />
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <x-input type="email" id="email" name="email" 
                                    value="{{ old('email', $user->email ?? '') }}" required />
                            </div>
                            <x-input-error for="email" />
                        </div>

                        {{-- Phone --}}
                        <div class="col-md-4">
                            <x-label for="phone_no" value="Phone No." />
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <x-input id="phone_no" name="phone_no" 
                                    value="{{ old('phone_no', $user->phone_no ?? '') }}" />
                            </div>
                            <x-input-error for="phone_no" />
                        </div>

                        {{-- Username --}}
                        <div class="col-md-4">
                            <x-label for="username" value="Username" />
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                <x-input id="username" name="username" 
                                    value="{{ old('username', $user->username ?? '') }}" />
                            </div>
                            <x-input-error for="username" />
                        </div>

                        {{-- Role --}}
                        <div class="col-md-4">
                            <x-bootstrap.dropdown id="role_id" name="role_id" label="Role" required
                                :items="$roles->map(fn($r) => ['value'=>$r->id,'label'=>ucfirst($r->name)])->toArray()"
                                :selected="old('role_id', $user->role_id ?? '')" />
                            <x-input-error for="role_id" />
                        </div>

                        {{-- Department --}}
                        <div class="col-md-4">
                            <x-bootstrap.dropdown id="department_id" name="department_id" label="Department" required
                                :items="$departments->map(fn($d)=>['value'=>$d->id,'label'=>$d->name])->toArray()"
                                :selected="old('department_id', $user->department_id ?? '')" />
                            <x-input-error for="department_id" />
                        </div>

                        {{-- Designation --}}
                        <div class="col-md-4">
                            <x-bootstrap.dropdown id="designation_id" name="designation_id" label="Designation"
                                :items="$designations->map(fn($d)=>['value'=>$d->id,'label'=>$d->title])->toArray()"
                                :selected="old('designation_id', $user->designation_id ?? '')" />
                            <x-input-error for="designation_id" />
                        </div>

                        {{-- Sub Department --}}
                        <div class="col-md-4">
                           <x-bootstrap.dropdown 
    id="sub_department_id" 
    name="sub_department_id" 
    label="Sub Department"
    :items="$subDepartments->map(fn($s) => [
        'value' => $s->id,
        'label' => $s->name . ' (' . ($s->department->name ?? 'N/A') . ')'
    ])->toArray()"
    :selected="old('sub_department_id', $user->sub_department_id ?? '')" 
/>

<x-input-error for="sub_department_id" />

                        </div>

                        {{-- Gender --}}
                        <div class="col-md-4">
                            <x-bootstrap.dropdown id="gender" name="gender" label="Gender"
                                :items="collect(['male','female','other'])->map(fn($g)=>['value'=>$g,'label'=>ucfirst($g)])->toArray()"
                                :selected="old('gender', $user->gender ?? '')" />
                            <x-input-error for="gender" />
                        </div>

                        {{-- District --}}
                        <div class="col-md-4">
                            <x-label for="district" value="District" />
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                <x-input id="district" name="district" 
                                    value="{{ old('district', $user->district ?? '') }}" />
                            </div>
                            <x-input-error for="district" />
                        </div>

                        {{-- Status --}}
                        <div class="col-md-4">
                            <x-bootstrap.dropdown id="status" name="status" label="Status" required
                                :items="[['value'=>'active','label'=>'Active'],['value'=>'inactive','label'=>'Inactive']]"
                                :selected="old('status', $user->status ?? '')" />
                            <x-input-error for="status" />
                        </div>

                        {{-- Password --}}
                        <div class="col-md-4">
                            <x-label for="password" value="Password" :required="!isset($user)" />
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <x-input type="password" id="password" name="password" autocomplete="new-password" />
                            </div>
                            @if(isset($user))
                                <small class="text-muted">Leave blank to keep current password</small>
                            @endif
                            <x-input-error for="password" />
                        </div>

                        {{-- Confirm Password --}}
                        <div class="col-md-4">
                            <x-label for="password_confirmation" value="Confirm Password" :required="!isset($user)" />
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <x-input type="password" id="password_confirmation" name="password_confirmation" />
                            </div>
                        </div>

                        {{-- Profile Photo --}}
                        <div class="col-md-4">
                            <x-label for="profile_photo" value="Profile Photo" />
                            <div class="mb-2">
                                <img id="profilePreview"
                                     src="{{ isset($user) ? $user->profile_photo_url : '' }}"
                                     class="rounded-circle {{ isset($user) && $user->profile_photo_url ? '' : 'd-none' }}"
                                     width="80" height="80" alt="Profile Photo Preview">
                            </div>
                            <input type="file" id="profile_photo" name="profile_photo" 
                                class="form-control" accept="image/*" />
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end border-top pt-3">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas {{ isset($user) ? 'fa-save' : 'fa-user-plus' }} me-2"></i>
                            {{ isset($user) ? 'Update User' : 'Create User' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Profile Photo Preview -->
    <script>
        document.getElementById('profile_photo')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('profilePreview');
            if (file && preview) {
                const reader = new FileReader();
                reader.onload = e => {
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</x-app-layout>

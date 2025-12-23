<x-app-layout>
    <div class="container-fluid">
        <x-admin.breadcrumb-header icon="fas fa-plus-circle text-primary" title="Assign Project" :breadcrumbs="[
            ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i> Dashboard'],
            ['route' => 'admin.package-project-assignments.index', 'label' => 'Assignments'],
            ['label' => 'Assign Project'],
        ]" />

        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Assign Project</h5>
            </div>
            <form method="POST" action="{{ route('admin.package-project-assignments.store') }}">
                @csrf
                <div class="card-body">

                    {{-- Department --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Department</label>
                        <select id="department_id" class="form-control" required>
                            <option value="">-- Choose Department --</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Projects --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Projects</label>

                        {{-- Select All Button --}}
                        <div id="select_all_wrapper" class="mb-2 d-none">
                            <button type="button" id="toggle_select_all" class="btn btn-sm btn-outline-primary">
                                Select All
                            </button>
                        </div>

                        <div id="project_checkboxes" class="border rounded p-3"
                            style="max-height: 250px; overflow-y: auto;">
                            <p class="text-muted mb-0">-- Select Department First --</p>
                        </div>
                        @error('package_project_ids')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>


                    {{-- Sub-Department --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Assign to Sub Department</label>
                        <select name="sub_department_id" id="sub_department_id" class="form-control">
                            <option value="">-- None --</option>
                            @foreach ($subDepartments as $subDept)
                                <option value="{{ $subDept->id }}">{{ $subDept->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">If selected, project will be assigned to all users in this
                            sub-department.</small>
                    </div>

                    <div class="text-center my-3">
                        <span class="badge bg-secondary">OR</span>
                    </div>

                    {{-- User --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Assign to User</label>
                        <select name="assigned_to" id="assigned_to" class="form-control">
                            <option value="">-- None --</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">If selected, project will be assigned only to this user.</small>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary">
                        <i class="fas fa-save"></i> Assign
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

{{-- Ajax Script for Projects --}}
<script>
    document.getElementById('department_id').addEventListener('change', function() {
        let deptId = this.value;
        let projectBox = document.getElementById('project_checkboxes');
        let selectAllWrapper = document.getElementById('select_all_wrapper');
        let toggleBtn = document.getElementById('toggle_select_all');

        projectBox.innerHTML = '<p class="text-muted">Loading projects...</p>';
        selectAllWrapper.classList.add('d-none'); // hide button until projects load

        if (deptId) {
            // Use Laravel route() helper
            let url = "{{ route('admin.package-projects.by-department', ':id') }}".replace(':id', deptId);

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    projectBox.innerHTML = '';
                    if (data.length) {
                        // show Select All button
                        selectAllWrapper.classList.remove('d-none');

                        data.forEach(p => {
                            let wrapper = document.createElement('div');
                            wrapper.className = "form-check mb-2";

                            let checkbox = document.createElement('input');
                            checkbox.type = "checkbox";
                            checkbox.name = "package_project_ids[]";
                            checkbox.value = p.id;
                            checkbox.id = "project_" + p.id;
                            checkbox.className = "form-check-input project-checkbox";

                            let label = document.createElement('label');
                            label.htmlFor = "project_" + p.id;
                            label.className = "form-check-label";
                            label.textContent = p.package_name;

                            wrapper.appendChild(checkbox);
                            wrapper.appendChild(label);
                            projectBox.appendChild(wrapper);
                        });

                        // reset button text
                        toggleBtn.textContent = "Select All";

                        // attach event listener for toggle
                        toggleBtn.onclick = function() {
                            let checkboxes = document.querySelectorAll('.project-checkbox');
                            let allChecked = Array.from(checkboxes).every(cb => cb.checked);

                            checkboxes.forEach(cb => cb.checked = !allChecked);

                            toggleBtn.textContent = allChecked ? "Select All" : "Deselect All";
                        };
                    } else {
                        projectBox.innerHTML =
                            '<p class="text-danger">No projects found for this department.</p>';
                    }
                })
                .catch(() => {
                    projectBox.innerHTML = '<p class="text-danger">Error loading projects.</p>';
                });
        } else {
            projectBox.innerHTML = '<p class="text-muted">-- Select Department First --</p>';
        }
    });
</script>

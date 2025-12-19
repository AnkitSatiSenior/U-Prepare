<x-guest-layout>
    @section('page_title', 'Register Grievance')
    <style>
        .head h1 { font-size: 1.8rem; }
        .head+hr { border: 2px solid var(--color-tblue); opacity: 1; }
        label sup { color: rgba(var(--bs-danger-rgb)); }
        .lh-1 * { line-height: 1; }
        .form-control.disabled { background-color: var(--bs-secondary-bg); }
    </style>

    {{-- Intro Message --}}
    <section class="container-xxl pt-4">
        <div class="row mb-3">
            <div class="col-12">
                <p class="mb-0 text-danger text-center text-uppercase">
                    You can inform us about any issue/complaint related to all projects through the website
                </p>
            </div>
        </div>
    </section>

    <section class="grievance-register p-0">
        <div class="head container-xxl">
            <h1 class="text-uppercase fw-bold text-dark m-0">Register Grievance</h1>
        </div>
        <hr class="mt-2 mb-5" />

        {{-- Alerts --}}
        @if(session('success'))
            <div class="container"><div class="alert alert-success">{!! session('success') !!}</div></div>
        @endif
        @if($errors->any())
            <div class="container"><div class="alert alert-danger">{{ $errors->first() }}</div></div>
        @endif

        <div class="container">
            <form method="POST" action="{{ route('grievances.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">

                    {{-- Full Name --}}
                    <div class="col-12 mb-3">
                        <label for="full-name">Full Name</label>
                        <input type="text" id="full-name" name="full_name"
                               class="form-control @error('full_name') is-invalid @enderror"
                               value="{{ old('full_name') }}"
                               placeholder="Grievance can be filed anonymously also">
                        @error('full_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    {{-- Address --}}
                    <div class="col-12 mb-3">
                        <label for="address">Address</label>
                        <input type="text" id="address" name="address"
                               class="form-control @error('address') is-invalid @enderror"
                               value="{{ old('address') }}">
                        @error('address') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    {{-- Email + Phone --}}
                    <div class="col-md-6 mb-3">
                        <label for="email">E-Mail ID</label>
                        <input type="email" id="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}">
                        @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone"><sup>*</sup>Mobile No.</label>
                        <input type="text" id="phone" name="phone"
                               class="form-control @error('phone') is-invalid @enderror"
                               value="{{ old('phone') }}" required>
                        @error('phone') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    {{-- Typology --}}
                    <div class="col-md-4 mb-3">
                        <label><sup>*</sup>Grievance related to</label>
                        <select name="typology" id="typology" class="form-control" required>
                            <option value="">Kindly Choose...</option>
                            @foreach($typology as $typo)
                                <option value="{{ $typo->slug }}" @selected(old('typology') == $typo->slug)>{{ $typo->name }}</option>
                            @endforeach
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-8 mb-3 d-none" id="typology-other-wrap">
                        <label>Please Specify</label>
                        <input type="text" name="typo_other" class="form-control" value="{{ old('typo_other') }}">
                    </div>

                    {{-- Category --}}
                    <div class="col-md-4 mb-3">
                        <label><sup>*</sup>Nature of Complaint</label>
                        <select name="category" id="category" class="form-control" required>
                            <option value="">Kindly Choose...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Subcategory --}}
                    <div class="col-md-4 mb-3">
                        <label><sup>*</sup>Detail of Complaint</label>
                        <select name="subcategory" id="subcategory" class="form-control" required>
                            <option value="">Kindly Choose...</option>
                        </select>
                    </div>
                    <div class="col-12 mb-3 d-none" id="subcategory-other-wrap">
                        <label>Please Specify</label>
                        <input type="text" name="scat_other" class="form-control">
                    </div>

                    {{-- District --}}
                    <div class="col-md-6 mb-3">
                        <label><sup>*</sup>District</label>
                        <select name="district" id="district" class="form-control" required>
                            <option value="">Kindly Choose...</option>
                            @foreach($districts as $dist)
                                <option value="{{ $dist->id }}">{{ $dist->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Project --}}
                    <div class="col-md-6 mb-3">
                        <label><sup>*</sup>Project</label>
                        <select name="project" id="project" class="form-control" required>
                            <option value="">Kindly Choose...</option>
                        </select>
                    </div>
                    <div class="col-md-8 mb-3 d-none" id="project-other-wrap">
                        <label>Please Specify</label>
                        <input type="text" name="proj_other" class="form-control">
                    </div>

                    {{-- Village --}}
                    <div class="col-12 mb-3">
                        <label>Village</label>
                        <input type="text" name="village" class="form-control" value="{{ old('village') }}">
                    </div>

                    {{-- Description --}}
                    <div class="col-12 mb-3">
                        <label>Description</label>
                        <textarea name="description" rows="5" class="form-control">{{ old('description') }}</textarea>
                    </div>

                    {{-- File Upload --}}
                    <div class="col-12 mb-3">
                        <label>Upload Document (If Any)</label>
                        <input type="file" name="file" class="form-control"
                               accept="image/jpg,image/jpeg,application/pdf,video/mp4">
                    </div>

                    {{-- Radios --}}
                    <div class="col-12 mb-3">
                        <span>Are you filing on behalf of someone else?</span><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="behalf" value="yes"> Yes
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="behalf" value="no"> No
                        </div>
                    </div>

                    <div class="col-12 mb-3">
                        <span>Do you have consent from survivor to share this information?</span><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="consent" value="yes"> Yes
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="consent" value="no"> No
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-lg">Submit Grievance</button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    {{-- JS for dependent dropdowns --}}
    <script>
        const categories = @json($categories);
        const districts  = @json($districts);
        const projects   = @json($projects);

        // Typology Other
        document.getElementById('typology').addEventListener('change', function() {
            document.getElementById('typology-other-wrap').classList.toggle('d-none', this.value !== 'other');
        });

        // Category → Subcategory
        document.getElementById('category').addEventListener('change', function() {
            const subcat = document.getElementById('subcategory');
            subcat.innerHTML = '<option value="">Kindly Choose...</option>';
            const selected = categories.find(c => c.id == this.value);
            if (selected && selected.details) {
                selected.details.forEach(sc => {
                    subcat.innerHTML += `<option value="${sc.id}">${sc.name}</option>`;
                });
                subcat.innerHTML += `<option value="other">Other</option>`;
            }
        });
        document.getElementById('subcategory').addEventListener('change', function() {
            document.getElementById('subcategory-other-wrap').classList.toggle('d-none', this.value !== 'other');
        });

        // District → Project (no block dependency)
        document.getElementById('district').addEventListener('change', function() {
            const project = document.getElementById('project');
            project.innerHTML = '<option value="">Kindly Choose...</option>';
            const distId = this.value;
            projects.forEach(p => {
                if (p.district_id == distId) {
                    project.innerHTML += `<option value="${p.id}">${p.package_name}</option>`;
                }
            });
            project.innerHTML += `<option value="other">Other</option>`;
        });

        // Project Other
        document.getElementById('project').addEventListener('change', function() {
            document.getElementById('project-other-wrap').classList.toggle('d-none', this.value !== 'other');
        });
    </script>
</x-guest-layout>

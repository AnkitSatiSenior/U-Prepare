<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 font-weight-bold text-dark mb-0">
                Update Professional Profile: <span class="text-muted">{{ $user->name }}</span>
            </h2>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-4">
                &larr; Back to Users
            </a>
        </div>
    </x-slot>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-sm-5">
                        
                        <form action="{{ route('admin.profile.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <h5 class="fw-bold mb-4 border-bottom pb-2">Timeline & Experience</h5>

                            <div class="row g-4 mb-5">
                                <div class="col-md-4">
                                    <label class="form-label text-muted fw-medium small mb-1">Date of Birth</label>
                                    <input type="date" name="dob" 
                                           class="form-control shadow-none @error('dob') is-invalid @enderror" 
                                           value="{{ old('dob', $user->dob ? \Carbon\Carbon::parse($user->dob)->format('Y-m-d') : '') }}">
                                    @error('dob') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label text-muted fw-medium small mb-1">Date of Joining</label>
                                    <input type="date" name="date_of_joining" 
                                           class="form-control shadow-none @error('date_of_joining') is-invalid @enderror" 
                                           value="{{ old('date_of_joining', $user->date_of_joining ? \Carbon\Carbon::parse($user->date_of_joining)->format('Y-m-d') : '') }}">
                                    @error('date_of_joining') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label text-muted fw-medium small mb-1">Total Work Experience</label>
                                    <input type="text" name="total_work_experience" 
                                           class="form-control shadow-none @error('total_work_experience') is-invalid @enderror" 
                                           placeholder="e.g., 10+ years"
                                           value="{{ old('total_work_experience', $user->total_work_experience) }}">
                                    @error('total_work_experience') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <h5 class="fw-bold mb-4 border-bottom pb-2">Professional Details</h5>

                            <div class="row g-4 mb-4">
                                <div class="col-12">
                                    <label class="form-label text-muted fw-medium small mb-1">Qualification(s)</label>
                                    <textarea name="qualification" rows="2" 
                                              class="form-control shadow-none @error('qualification') is-invalid @enderror"
                                              placeholder="B.Tech in Electronic & Communication...">{{ old('qualification', $user->qualification) }}</textarea>
                                    @error('qualification') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label text-muted fw-medium small mb-1">Area of Expertise</label>
                                    <textarea name="area_of_expertise" rows="3" 
                                              class="form-control shadow-none @error('area_of_expertise') is-invalid @enderror"
                                              placeholder="Hardware Networking, Cloud Computing...">{{ old('area_of_expertise', $user->area_of_expertise) }}</textarea>
                                    @error('area_of_expertise') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label text-muted fw-medium small mb-1">Research / Publications / Citations</label>
                                    <input type="text" name="research_publication_citation" 
                                           class="form-control shadow-none @error('research_publication_citation') is-invalid @enderror" 
                                           placeholder="Leave blank or write 'No' if none"
                                           value="{{ old('research_publication_citation', $user->research_publication_citation) }}">
                                    @error('research_publication_citation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label text-muted fw-medium small mb-1">Previous Experience (Last 3 Organizations)</label>
                                    <textarea name="previous_experience" rows="4" 
                                              class="form-control shadow-none @error('previous_experience') is-invalid @enderror"
                                              placeholder="Details of previous employment...">{{ old('previous_experience', $user->previous_experience) }}</textarea>
                                    @error('previous_experience') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-3 mt-5">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-light px-4 rounded-pill fw-medium">Cancel</a>
                                <button type="submit" class="btn btn-dark px-5 rounded-pill fw-medium shadow-sm">Save Profile Changes</button>
                            </div>
                            
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
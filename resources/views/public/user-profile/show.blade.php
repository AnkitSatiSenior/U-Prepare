<x-guest-layout>
    @section('page_title', $user->name . ' - Professional Profile')

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                
                <!-- Profile Card -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    
                    <!-- Subtle Header Banner -->
                    <div class="bg-light border-bottom" style="height: 100px;"></div>

                    <div class="card-body px-sm-5 pb-5 pt-0 position-relative">
                        
                        <!-- Centered Avatar (Overlapping the banner) -->
                        <div class="text-center mb-4" style="margin-top: -50px;">
                            <img src="{{ $user->profile_photo_url }}" 
                                 alt="{{ $user->name }}'s Profile"
                                 class="rounded-circle border border-4 border-white shadow-sm" 
                                 width="120" height="120" 
                                 style="object-fit: cover; background-color: #fff;">
                        </div>

                        <!-- Header Information -->
                        <div class="text-center mb-5">
                            <h2 class="fw-bold text-dark mb-1">{{ $user->name }}</h2>
                            <p class="text-muted fs-5 mb-2">{{ $user->designation?->title ?? 'Professional' }}</p>
                            
                            @if($user->date_of_joining)
                                <span class="badge bg-secondary bg-opacity-10 text-white border fw-normal px-3 py-2 rounded-pill">
                                    Joined {{ $user->date_of_joining->format('F d, Y') }}
                                </span>
                            @endif
                        </div>

                        <!-- Profile Data Table -->
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0">
                                <tbody>
                                    <tr class="border-bottom">
                                        <th scope="row" class="text-muted fw-normal py-3" style="width: 40%;">Full Name</th>
                                        <td class="py-3 fw-medium text-dark">{{ $user->name }}</td>
                                    </tr>

                                    <tr class="border-bottom">
                                        <th scope="row" class="text-muted fw-normal py-3">Current Position</th>
                                        <td class="py-3 text-dark">{{ $user->designation?->title ?? '-' }}</td>
                                    </tr>

                                    <tr class="border-bottom">
                                        <th scope="row" class="text-muted fw-normal py-3 align-top">Qualification(s)</th>
                                        <td class="py-3 text-dark">
                                            <div style="white-space: pre-wrap;">{{ $user->qualification ?? '-' }}</div>
                                        </td>
                                    </tr>

                                    <tr class="border-bottom">
                                        <th scope="row" class="text-muted fw-normal py-3">Post Qualification Experience</th>
                                        <td class="py-3 text-dark">{{ $user->total_work_experience ?? '-' }}</td>
                                    </tr>

                                    <tr class="border-bottom">
                                        <th scope="row" class="text-muted fw-normal py-3 align-top">Area of Expertise</th>
                                        <td class="py-3 text-dark">
                                            <div style="white-space: pre-wrap; line-height: 1.6;">{{ $user->area_of_expertise ?? '-' }}</div>
                                        </td>
                                    </tr>

                                    <tr class="border-bottom">
                                        <th scope="row" class="text-muted fw-normal py-3 align-top">Research, Publications & Citations</th>
                                        <td class="py-3 text-dark">
                                            <div style="white-space: pre-wrap;">{{ $user->research_publication_citation ?? '-' }}</div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th scope="row" class="text-muted fw-normal py-3 align-top">Previous Experience<br><small class="text-light-muted">(Last 3 Organizations)</small></th>
                                        <td class="py-3 text-dark">
                                            <div style="white-space: pre-wrap; line-height: 1.6;">{{ $user->previous_experience ?? '-' }}</div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</x-guest-layout>
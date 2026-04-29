<x-guest-layout>
    @section('page_title', 'Contact Us')
    
    <main class="container py-5 my-md-5">
        <header class="row justify-content-center mb-5 text-center">
            <div class="col-lg-8">
                <h1 class="fw-bold text-dark mb-2">Contact <span class="text-theme">U-Prepare</span></h1>
               
            </div>
        </header>

        <div class="row g-5 justify-content-center">
            <div class="col-lg-5 col-md-6">
                <div class="p-4 p-md-5 bg-light rounded-4 shadow-sm h-100 border border-light-subtle d-flex flex-column">
                     <h3 class="h4 fw-semibold mb-4 text-dark">
                        <i class="bi bi-headset me-2 text-theme"></i>Get in Touch
                    </h3>
                    
                    <div class="d-flex flex-column gap-3 text-muted mt-auto">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-telephone fs-5 text-theme me-3"></i>
                            <a href="tel:1800-180-4276" class="text-decoration-none text-muted">1800-180-4276</a></br>
                            <a href="tel:+91-7906309285" class="text-decoration-none text-muted">+91 79063 09285</a>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-envelope fs-5 text-theme me-3"></i>
                            <a href="mailto:contact@u-prepare.com" class="text-decoration-none text-muted">contact@u-prepare.com</a>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-globe fs-5 text-theme me-3"></i>
                            <a href="https://www.u-prepare.com" target="_blank" rel="noopener noreferrer" class="text-decoration-none text-muted">www.u-prepare.com</a>
                        </div>
                    </div>
                    <h3 class="h4 fw-semibold mb-4 text-dark">
                        <i class="bi bi-building me-2 text-theme"></i>Our Office
                    </h3>
                    
                    <address class="text-muted mb-4">
                        <div class="d-flex mb-3">
                            <i class="bi bi-geo-alt fs-5 text-theme me-3"></i>
                            <span>
                                <strong>USDMA</strong><br>
                                IT Park, Sahastradhara Road<br>
                                Dehradun, Uttarakhand, India
                            </span>
                        </div>
                    </address>

                    <div class="ratio ratio-16x9 mb-5 rounded-3 overflow-hidden shadow-sm border border-light-subtle">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3442.544205053256!2d78.08280947666888!3d30.3639011033622!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3908d70048644c07%3A0xa0a0da3e097c93a4!2sUSDMA%20New%20Building%20IT%20park!5e0!3m2!1sen!2sin!4v1777462587826!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>

                   
                </div>
            </div>

            <div class="col-lg-6 col-md-6">
                <div class="p-4 p-md-5 bg-white rounded-4 shadow-sm h-100 border border-light-subtle">
                    <h3 class="h4 fw-semibold mb-4 text-dark">
                        <i class="bi bi-chat-left-dots me-2 text-theme"></i>Send a Message
                    </h3>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show d-flex align-items-start shadow-sm border-0 bg-success-subtle text-success-emphasis" role="alert">
                            <i class="bi bi-check-circle-fill fs-4 me-3 mt-1"></i>
                            <div>
                                <h5 class="alert-heading fw-bold mb-1">Success!</h5>
                                <p class="mb-0">We have received your message. Our team will contact you soon.</p>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 bg-danger-subtle text-danger-emphasis" role="alert">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                                <strong class="fs-6">Please correct the following errors:</strong>
                            </div>
                            <ul class="mb-0 ps-4">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('feedback.store') }}" method="POST" novalidate>
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="visually-hidden">Full Name</label>
                            <input type="text" id="name" name="name" 
                                class="form-control form-control-lg bg-light border-0 px-4 py-3 @error('name') is-invalid @enderror"
                                placeholder="Full Name *" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback fw-medium px-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="email" class="visually-hidden">Email Address</label>
                            <input type="email" id="email" name="email" 
                                class="form-control form-control-lg bg-light border-0 px-4 py-3 @error('email') is-invalid @enderror"
                                placeholder="Email Address *" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback fw-medium px-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="type" class="visually-hidden">Query Type</label>
                            <select name="type" class="form-control mb-3 @error('type') is-invalid @enderror">
                            <option value="">Kindly Select Query Type</option>
                            <option value="inquiry" {{ old('type')=='inquiry' ? 'selected' : '' }}>INQUIRY</option>
                            <option value="feedback" {{ old('type')=='feedback' ? 'selected' : '' }}>FEEDBACK
                            </option>
                            <option value="others" {{ old('type')=='others' ? 'selected' : '' }}>OTHERS</option>
                        </select>
                            @error('type')
                                <div class="invalid-feedback fw-medium px-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="subject" class="visually-hidden">Subject</label>
                            <input type="text" id="subject" name="subject" 
                                class="form-control form-control-lg bg-light border-0 px-4 py-3 @error('subject') is-invalid @enderror"
                                placeholder="Subject" value="{{ old('subject') }}">
                            @error('subject')
                                <div class="invalid-feedback fw-medium px-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="message" class="visually-hidden">Message</label>
                            <textarea id="message" name="message" rows="5" 
                                class="form-control form-control-lg bg-light border-0 px-4 py-3 @error('message') is-invalid @enderror"
                                placeholder="Your Message *" required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback fw-medium px-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-theme btn-lg py-3 fw-bold shadow-sm rounded-3">
                                <i class="bi bi-send-fill me-2"></i> Submit Inquiry
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</x-guest-layout>
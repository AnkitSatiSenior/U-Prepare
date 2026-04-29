<x-guest-layout>
    @section('page_title', 'Contact Us')

    <style>
        :root {
            /* Fallback colors assuming you have a theme setup */
            --theme-color: #0d6efd; 
            --theme-color-hover: #0b5ed7;
        }
        
        /* Smooth transitions & Hover states */
        .contact-card {
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        .contact-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
            background-color: #ffffff !important;
        }
        
        /* Icon styling */
        .icon-box {
            width: 52px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        .contact-card:hover .icon-box {
            background-color: var(--theme-color) !important;
            color: #ffffff !important;
        }

        /* Form styling */
        .form-control, .form-select {
            transition: all 0.2s ease-in-out;
            background-color: #f8f9fa;
        }
        .form-control:focus, .form-select:focus {
            background-color: #ffffff;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
            border-color: var(--theme-color);
        }

        /* Button styling */
        .btn-theme {
            background-color: var(--theme-color);
            color: #fff;
            transition: all 0.3s ease;
        }
        .btn-theme:hover {
            background-color: var(--theme-color-hover);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(13, 110, 253, 0.2);
        }
        
        .text-theme { color: var(--theme-color); }
    </style>

    <main class="container py-5 my-md-4">
        
        <header class="row justify-content-center mb-5 text-center">
            <div class="col-lg-6">
                <h1 class="fw-bold text-dark mb-3">Contact <span class="text-theme">Us</span></h1>
                <p class="text-muted fs-5">We're here to help. Reach out to our team through any of the channels below or send us a direct message.</p>
            </div>
        </header>

        <div class="row g-5 justify-content-between align-items-start">
            
            <div class="col-lg-5 col-md-12">
                <div class="pe-lg-4">
                    <h3 class="h4 fw-semibold mb-4 text-dark">Get in Touch</h3>

                    <a href="tel:18001804276" class="contact-card bg-light p-3 rounded-4 mb-3 border border-light-subtle shadow-sm">
                        <div class="icon-box bg-white text-theme shadow-sm me-4 fs-4">
                            <i class="bi bi-headset"></i>
                        </div>
                        <div>
                            <span class="d-block text-muted small fw-bold text-uppercase tracking-wide mb-1">Toll-Free Support</span>
                            <span class="d-block text-dark fw-bold fs-5">1800-180-4276</span>
                        </div>
                    </a>

                    <a href="tel:01352971663" class="contact-card bg-light p-3 rounded-4 mb-3 border border-light-subtle shadow-sm">
                        <div class="icon-box bg-white text-theme shadow-sm me-4 fs-4">
                            <i class="bi bi-telephone"></i>
                        </div>
                        <div>
                            <span class="d-block text-muted small fw-bold text-uppercase tracking-wide mb-1">Primary Contact</span>
                            <span class="d-block text-dark fw-bold fs-5">0135-297-1663</span>
                        </div>
                    </a>

                    <a href="tel:+917906309285" class="contact-card bg-light p-3 rounded-4 mb-3 border border-light-subtle shadow-sm">
                        <div class="icon-box bg-white text-theme shadow-sm me-4 fs-4">
                            <i class="bi bi-tools"></i>
                        </div>
                        <div>
                            <span class="d-block text-muted small fw-bold text-uppercase tracking-wide mb-1">Service & Technical</span>
                            <span class="d-block text-dark fw-bold fs-5">+91 79063-09285</span>
                        </div>
                    </a>

                    <a href="https://wa.me/917906309285" target="_blank" rel="noopener noreferrer" class="contact-card bg-light p-3 rounded-4 mb-4 border border-light-subtle shadow-sm">
                        <div class="icon-box bg-white text-success shadow-sm me-4 fs-4">
                            <i class="bi bi-whatsapp"></i>
                        </div>
                        <div>
                            <span class="d-block text-muted small fw-bold text-uppercase tracking-wide mb-1">WhatsApp Chat</span>
                            <span class="d-block text-dark fw-bold fs-5">+91 79063-09285</span>
                        </div>
                    </a>

                    <div class="mt-5 d-flex align-items-start">
                        <i class="bi bi-geo-alt-fill text-theme fs-4 me-3 mt-1"></i>
                        <address class="text-muted mb-0 lh-lg">
                            <strong class="text-dark d-block fs-5 mb-1">USDMA Headquarters</strong>
                            IT Park, Sahastradhara Road<br>
                            Dehradun, Uttarakhand, India
                        </address>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 col-md-12">
                <div class="p-4 p-md-5 bg-white rounded-4 shadow h-100 border border-light-subtle">
                    <h3 class="h4 fw-semibold mb-4 text-dark">Send a Message</h3>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm border-0 bg-success-subtle text-success-emphasis rounded-3" role="alert">
                            <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                            <div>
                                <h5 class="alert-heading fw-bold mb-0">Success!</h5>
                                <span class="small">We have received your message. Our team will contact you soon.</span>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 bg-danger-subtle text-danger-emphasis rounded-3" role="alert">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                                <strong class="fs-6">Please correct the following errors:</strong>
                            </div>
                            <ul class="mb-0 ps-4 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('feedback.store') }}" method="POST" novalidate>
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label small fw-semibold text-muted ms-1">Full Name *</label>
                                <input type="text" id="name" name="name"
                                    class="form-control form-control-lg border-0 px-4 py-3 rounded-3 @error('name') is-invalid @enderror"
                                    placeholder="John Doe" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback fw-medium px-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label small fw-semibold text-muted ms-1">Email Address *</label>
                                <input type="email" id="email" name="email"
                                    class="form-control form-control-lg border-0 px-4 py-3 rounded-3 @error('email') is-invalid @enderror"
                                    placeholder="john@example.com" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback fw-medium px-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label small fw-semibold text-muted ms-1">Phone Number</label>
                                <input type="tel" id="phone" name="phone_number"
                                    class="form-control form-control-lg border-0 px-4 py-3 rounded-3 @error('phone_number') is-invalid @enderror"
                                    placeholder="+91 XXXXX XXXXX" value="{{ old('phone_number') }}">
                                @error('phone_number')
                                    <div class="invalid-feedback fw-medium px-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label small fw-semibold text-muted ms-1">Query Type *</label>
                                <select id="type" name="type" class="form-select form-select-lg border-0 px-4 py-3 rounded-3 @error('type') is-invalid @enderror" required>
                                    <option value="" disabled {{ old('type') ? '' : 'selected' }}>Select an option</option>
                                    <option value="inquiry" {{ old('type') == 'inquiry' ? 'selected' : '' }}>General Inquiry</option>
                                    <option value="feedback" {{ old('type') == 'feedback' ? 'selected' : '' }}>Feedback</option>
                                    <option value="others" {{ old('type') == 'others' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback fw-medium px-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 mt-1">
                            <label for="subject" class="form-label small fw-semibold text-muted ms-1">Subject</label>
                            <input type="text" id="subject" name="subject"
                                class="form-control form-control-lg border-0 px-4 py-3 rounded-3 @error('subject') is-invalid @enderror"
                                placeholder="How can we help you?" value="{{ old('subject') }}">
                            @error('subject')
                                <div class="invalid-feedback fw-medium px-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="message" class="form-label small fw-semibold text-muted ms-1">Message *</label>
                            <textarea id="message" name="message" rows="4"
                                class="form-control form-control-lg border-0 px-4 py-3 rounded-3 @error('message') is-invalid @enderror"
                                placeholder="Write your message here..." required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback fw-medium px-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid mt-2">
                            <button type="submit" class="btn btn-theme btn-lg py-3 fw-bold rounded-3">
                                Send Message <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
        </div>
    </main>
</x-guest-layout>
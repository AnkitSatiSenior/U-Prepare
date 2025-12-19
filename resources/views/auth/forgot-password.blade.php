<x-guest-layout>
    @section('page_title', 'Forgot Password')
    @vite(['resources/js/app.js'])

    <div class="container min-vh-100 d-flex align-items-center justify-content-center">
        <div class="row w-100 shadow-lg rounded-4 overflow-hidden animate__animated animate__fadeIn">
            
            <!-- Left Side Image -->
            <div class="col-lg-6 d-none d-lg-block p-0">
                <img src="{{ asset('assets/public/img/mis-login.jpeg') }}" 
                     alt="Forgot Password Image"
                     class="w-100 h-100 object-fit-cover">
            </div>

            <!-- Right Side Form -->
            <div class="col-lg-6 bg-white d-flex align-items-center">
                <div class="w-100 p-5">

                    <!-- Heading -->
                    <div class="text-center mb-4">
                        <h4 class="fw-bold text-primary mb-1">
                            Uttarakhand Disaster Preparedness and Resilience Project
                        </h4>
                        <h2 class="fw-bolder text-dark">(U-PREPARE)</h2>
                    </div>

                    <!-- Info Message -->
                    <div class="alert alert-info small mb-4 animate__animated animate__fadeInDown">
                        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.') }}
                    </div>

                    <!-- Status Message -->
                    @if (session('status'))
                        <div class="alert alert-success small mb-4 animate__animated animate__fadeInUp">
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- Validation Errors -->
                    <x-validation-errors class="mb-3" />

                    <!-- Forgot Password Form -->
                    <form method="POST" action="{{ route('password.email') }}" class="animate__animated animate__fadeInUp">
                        @csrf

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">
                                <i class="fas fa-envelope me-2 text-secondary"></i>Email Address
                            </label>
                            <input id="email" 
                                   type="email" 
                                   name="email" 
                                   value="{{ old('email') }}"
                                   class="form-control form-control-lg rounded-3" 
                                   required autofocus 
                                   autocomplete="username">
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('login') }}" class="text-decoration-none small text-muted">
                                <i class="fas fa-arrow-left me-1"></i> Back to Login
                            </a>
                            <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm">
                                <i class="fas fa-paper-plane me-2"></i>{{ __('Email Password Reset Link') }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-guest-layout>

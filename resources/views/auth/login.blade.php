<x-guest-layout>
    @section('page_title', __('Login'))
    
    @vite(['resources/js/app.js'])

    <div class="container min-vh-100 d-flex align-items-center justify-content-center py-5">
        <div class="row w-100 shadow-lg rounded-4 overflow-hidden bg-white animate__animated animate__fadeIn" style="max-width: 1000px;">

            <div class="col-lg-6 d-none d-lg-block p-0 position-relative bg-light">
                <img src="{{ asset('assets/public/img/mis-login.jpeg') }}" 
                     alt="{{ __('U-PREPARE Login Graphic') }}"
                     class="w-100 h-100 object-fit-cover"
                     loading="lazy">
            </div>

            <div class="col-lg-6 d-flex align-items-center">
                <div class="w-100 p-4 p-md-5">

                    <header class="text-center mb-5">
                        <h1 class="h5 fw-bold text-primary mb-2">
                            {{ __('Uttarakhand Disaster Preparedness and Resilience Project') }}
                        </h1>
                        <p class="h4 fw-bolder text-dark mb-0">{{ __('(U-PREPARE)') }}</p>
                    </header>

                    <x-validation-errors class="mb-4" />

                    @if (session('status'))
                        <div class="alert alert-success small" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="animate__animated animate__fadeInUp" novalidate>
                        @csrf

                        <div class="mb-4">
                            <label for="login" class="form-label fw-semibold">
                                <i class="fas fa-user me-2 text-secondary" aria-hidden="true"></i>{{ __('Email or Username') }}
                            </label>
                            <input id="login" type="text" name="login" value="{{ old('login') }}"
                                class="form-control form-control-lg rounded-3 @error('login') is-invalid @enderror" 
                                required autofocus autocomplete="username">
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold">
                                <i class="fas fa-lock me-2 text-secondary" aria-hidden="true"></i>{{ __('Password') }}
                            </label>
                            
                            <div class="input-group input-group-lg">
                                <input id="password" type="password" name="password"
                                    class="form-control rounded-start-3 border-end-0 @error('password') is-invalid @enderror" 
                                    required autocomplete="current-password">
                                <button class="btn border border-start-0 rounded-end-3 bg-white" 
                                        type="button" 
                                        id="togglePassword"
                                        aria-label="{{ __('Toggle password visibility') }}">
                                    <i class="fas fa-eye-slash text-muted" id="toggleIcon" aria-hidden="true"></i>
                                </button>
                            </div>

                            @if (Route::has('password.request'))
                                <div class="text-end mt-2">
                                    <a class="text-decoration-none small text-muted"
                                       href="{{ route('password.request') }}">
                                        <i class="fas fa-key me-1" aria-hidden="true"></i> {{ __('Forgot your password?') }}
                                    </a>
                                </div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-5">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
                                <label class="form-check-label small text-muted" for="remember_me">
                                    {{ __('Remember me') }}
                                </label>
                            </div>
                            <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm">
                                <i class="fas fa-sign-in-alt me-2" aria-hidden="true"></i>{{ __('Log In') }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('password');
            const toggleBtn = document.getElementById('togglePassword');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordInput && toggleBtn && toggleIcon) {
                toggleBtn.addEventListener('click', function () {
                    // 1. Toggle the type attribute
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');

                    // 2. Toggle the FontAwesome icon classes
                    if (isPassword) {
                        toggleIcon.classList.remove('fa-eye-slash');
                        toggleIcon.classList.add('fa-eye', 'text-primary'); // Highlight when visible
                        toggleIcon.classList.remove('text-muted');
                        toggleBtn.setAttribute('aria-label', '{{ __("Hide password") }}');
                    } else {
                        toggleIcon.classList.remove('fa-eye', 'text-primary');
                        toggleIcon.classList.add('fa-eye-slash', 'text-muted');
                        toggleBtn.setAttribute('aria-label', '{{ __("Show password") }}');
                    }
                });
            }
        });
    </script>
  
</x-guest-layout>
<x-guest-layout>
    @section('page_title', 'Login')
    @vite(['resources/js/app.js'])

    <div class="container min-vh-100 d-flex align-items-center justify-content-center">
        <div class="row w-100 shadow-lg rounded-4 overflow-hidden animate__animated animate__fadeIn">

            <!-- Left Side Image -->
            <div class="col-lg-6 d-none d-lg-block p-0">
                <img src="{{ asset('assets/public/img/mis-login.jpeg') }}" alt="Login Image"
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

                    <!-- Validation Errors -->
                    <x-validation-errors class="mb-3" />

                    @if (session('status'))
                        <div class="alert alert-success small">
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login') }}" class="animate__animated animate__fadeInUp">
                        @csrf

                        <!-- Email / Username -->
                        <div class="mb-3">
                            <label for="login" class="form-label fw-semibold">
                                <i class="fas fa-user me-2 text-secondary"></i>Email or Username
                            </label>
                            <input id="login" type="text" name="login" value="{{ old('login') }}"
                                class="form-control form-control-lg rounded-3" required autofocus
                                autocomplete="username">
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">
                                <i class="fas fa-lock me-2 text-secondary"></i>Password
                            </label>
                            <div class="input-group input-group-lg">
                                <input id="password" type="password" name="password"
                                    class="form-control rounded-start-3" required autocomplete="current-password">
                                <button class="btn btn-outline-secondary rounded-end-3" type="button"
                                    id="togglePassword">
                                    <i class="fas fa-eye-slash"></i>
                                </button>
                            </div>
                            <!-- Forgot Password Link -->
                            @if (Route::has('password.request'))
                                <div class="text-end mt-2">
                                    <a class="text-decoration-none small text-muted"
                                       href="{{ route('password.request') }}">
                                        <i class="fas fa-key me-1"></i> Forgot your password?
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Remember + Button -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
                                <label class="form-check-label small text-muted" for="remember_me">
                                    Remember me
                                </label>
                            </div>
                            <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm">
                                <i class="fas fa-sign-in-alt me-2"></i>Log In
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

</x-guest-layout>

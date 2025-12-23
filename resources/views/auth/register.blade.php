<x-guest-layout>
    @section('page_title', 'Login')

    <div class="container py-5">
        <div class="row align-items-center">
            
            <!-- Left Side Image -->
            <div class="col-lg-5 mb-4 mb-lg-0">
                <img src="{{ asset('assets/public/img/mis-login.jpeg') }}" 
                     alt="Login Image" 
                     class="img-fluid rounded shadow">
            </div>

            <!-- Right Side Form -->
            <div class="col-lg-7">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        
                        <!-- Heading -->
                        <div class="text-center mb-4">
                            <h4 class="fw-bold text-primary">
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
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <!-- Email / Username -->
                            <div class="mb-3">
                                <label for="login" class="form-label fw-semibold">
                                    <i class="fas fa-user me-2 text-secondary"></i>Email or Username
                                </label>
                                <input id="login" 
                                       type="text" 
                                       name="login" 
                                       value="{{ old('login') }}"
                                       class="form-control" 
                                       required autofocus 
                                       autocomplete="username">
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">
                                    <i class="fas fa-lock me-2 text-secondary"></i>Password
                                </label>
                                <div class="input-group">
                                    <input id="password" 
                                           type="password" 
                                           name="password"
                                           class="form-control" 
                                           required autocomplete="current-password">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye-slash"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Remember + Button -->
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
                                    <label class="form-check-label small text-muted" for="remember_me">
                                        Remember me
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-sign-in-alt me-2"></i>Log In
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Show/Hide Password Script -->
    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    </script>
</x-guest-layout>

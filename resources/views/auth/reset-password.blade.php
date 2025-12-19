<x-guest-layout>
    @section('page_title', 'Reset Password')

    <div class="container py-5">
        <div class="row align-items-center">
            
            <!-- Left Side Image -->
            <div class="col-lg-5 mb-4 mb-lg-0">
                <img src="{{ asset('assets/public/img/mis-login.jpeg') }}" 
                     alt="Reset Password Image" 
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

                        <!-- Reset Password Form -->
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf

                            <input type="hidden" name="token" value="{{ $request->route('token') }}">

                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">
                                    <i class="fas fa-envelope me-2 text-secondary"></i>Email Address
                                </label>
                                <input id="email" 
                                       type="email" 
                                       name="email" 
                                       value="{{ old('email', $request->email) }}"
                                       class="form-control" 
                                       required autofocus 
                                       autocomplete="username">
                            </div>

                            <!-- New Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">
                                    <i class="fas fa-lock me-2 text-secondary"></i>New Password
                                </label>
                                <div class="input-group">
                                    <input id="password" 
                                           type="password" 
                                           name="password"
                                           class="form-control" 
                                           required autocomplete="new-password">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye-slash"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label fw-semibold">
                                    <i class="fas fa-check-double me-2 text-secondary"></i>Confirm Password
                                </label>
                                <div class="input-group">
                                    <input id="password_confirmation" 
                                           type="password" 
                                           name="password_confirmation"
                                           class="form-control" 
                                           required autocomplete="new-password">
                                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                        <i class="fas fa-eye-slash"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Reset Button -->
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-redo me-2"></i>Reset Password
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

        document.getElementById('toggleConfirmPassword').addEventListener('click', function () {
            const password = document.getElementById('password_confirmation');
            const icon = this.querySelector('i');
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    </script>
</x-guest-layout>

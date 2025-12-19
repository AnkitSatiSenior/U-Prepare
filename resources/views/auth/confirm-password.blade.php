<x-guest-layout>
    @section('page_title', 'Confirm Password')

    <div class="container py-5">
        <div class="row align-items-center">
            
            <!-- Left Side Image -->
            <div class="col-lg-5 mb-4 mb-lg-0">
                <img src="{{ asset('assets/public/img/mis-login.jpeg') }}" 
                     alt="Confirm Password Image" 
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

                        <!-- Info Message -->
                        <div class="alert alert-info small mb-4">
                            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
                        </div>

                        <!-- Validation Errors -->
                        <x-validation-errors class="mb-3" />

                        <!-- Confirm Password Form -->
                        <form method="POST" action="{{ route('password.confirm') }}">
                            @csrf

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
                                           required autofocus 
                                           autocomplete="current-password">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye-slash"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Confirm Button -->
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-check-circle me-2"></i>Confirm
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

<x-guest-layout>
    @section('page_title', 'Verify Email')

    <div class="container py-5">
        <div class="row align-items-center">

            <!-- Left Side Image -->
            <div class="col-lg-5 mb-4 mb-lg-0">
                <img src="{{ asset('assets/public/img/mis-login.jpeg') }}" 
                     alt="Verify Email Image" 
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
                            {{ __('Before continuing, please verify your email address by clicking the link we just sent you. If you didn\'t receive the email, we can send another.') }}
                        </div>

                        <!-- Success Message -->
                        @if (session('status') == 'verification-link-sent')
                            <div class="alert alert-success small mb-4">
                                {{ __('A new verification link has been sent to the email address you provided in your profile settings.') }}
                            </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <!-- Resend Verification Email -->
                            <form method="POST" action="{{ route('verification.send') }}">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-envelope me-2"></i>{{ __('Resend Verification Email') }}
                                </button>
                            </form>

                            <!-- Edit Profile + Logout -->
                            <div class="text-end">
                                <a href="{{ route('profile.show') }}" class="btn btn-link small text-decoration-none">
                                    <i class="fas fa-user-edit me-1"></i>{{ __('Edit Profile') }}
                                </a>

                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-link small text-decoration-none text-danger">
                                        <i class="fas fa-sign-out-alt me-1"></i>{{ __('Log Out') }}
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>

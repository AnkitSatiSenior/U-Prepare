<x-guest-layout>
    @section('page_title', 'Two-Factor Authentication')

    <div class="container py-5">
        <div class="row align-items-center">

            <!-- Left Side Image -->
            <div class="col-lg-5 mb-4 mb-lg-0">
                <img src="{{ asset('assets/public/img/mis-login.jpeg') }}" 
                     alt="Two-Factor Authentication" 
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
                        <div x-data="{ recovery: false }">
                            <div class="alert alert-info small mb-4" x-show="! recovery">
                                {{ __('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}
                            </div>

                            <div class="alert alert-info small mb-4" x-cloak x-show="recovery">
                                {{ __('Please confirm access to your account by entering one of your emergency recovery codes.') }}
                            </div>

                            <!-- Validation Errors -->
                            <x-validation-errors class="mb-3" />

                            <!-- 2FA Form -->
                            <form method="POST" action="{{ route('two-factor.login') }}">
                                @csrf

                                <!-- Authenticator Code -->
                                <div class="mb-3" x-show="! recovery">
                                    <label for="code" class="form-label fw-semibold">
                                        <i class="fas fa-key me-2 text-secondary"></i>Authentication Code
                                    </label>
                                    <input id="code"
                                           type="text"
                                           inputmode="numeric"
                                           name="code"
                                           class="form-control"
                                           autofocus
                                           x-ref="code"
                                           autocomplete="one-time-code">
                                </div>

                                <!-- Recovery Code -->
                                <div class="mb-3" x-cloak x-show="recovery">
                                    <label for="recovery_code" class="form-label fw-semibold">
                                        <i class="fas fa-unlock-alt me-2 text-secondary"></i>Recovery Code
                                    </label>
                                    <input id="recovery_code"
                                           type="text"
                                           name="recovery_code"
                                           class="form-control"
                                           x-ref="recovery_code"
                                           autocomplete="one-time-code">
                                </div>

                                <!-- Actions -->
                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <button type="button"
                                            class="btn btn-link p-0 small text-decoration-none"
                                            x-show="! recovery"
                                            x-on:click="
                                                recovery = true;
                                                $nextTick(() => { $refs.recovery_code.focus() })
                                            ">
                                        <i class="fas fa-unlock me-1"></i>{{ __('Use a recovery code') }}
                                    </button>

                                    <button type="button"
                                            class="btn btn-link p-0 small text-decoration-none"
                                            x-cloak
                                            x-show="recovery"
                                            x-on:click="
                                                recovery = false;
                                                $nextTick(() => { $refs.code.focus() })
                                            ">
                                        <i class="fas fa-key me-1"></i>{{ __('Use an authentication code') }}
                                    </button>

                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-sign-in-alt me-2"></i>{{ __('Log in') }}
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>

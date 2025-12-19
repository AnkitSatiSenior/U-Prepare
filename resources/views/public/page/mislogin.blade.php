@extends('public.layout.base')

@section('page_title'){{ __('Login to MIS') }}@endsection

@section('header_styles')
    <style>
        main {
            display: flex;
            align-items: center;
            min-height: calc(100vh - 437px);
        }

        [type="checkbox"] {
            height: 1rem;
            widows: 1rem;
            display: inline-block;
            border-width: 1px;
            background-color: #f3f4f6;
            background-origin: border-box;
        }

        .login-sideimg {
            width: 90%;
            height: 520px;
            position: relative;
        }

        .login-sideimg > img {
            position: absolute;
            object-fit: cover;
        }

        .login-card {
            border: 4px solid black;
            background: #f3f4f6;
        }

        .login-head > h2 {
            font-size: 1.5rem;
        }

        .login-head > h4 {
            font-size: 1rem;
        }

        .login-body {
            min-width: 380px;
            border-radius: 6px;
        }

        .text-sm {
            font-size:.875rem;
            line-height: 1.25rem
        }

        .text-xs {
            font-size: .75rem;
            line-height: 1rem;
        }

        .text-gray-600 {
            --tw-text-opacity: 1;
            color:rgb(75 85 99 / var(--tw-text-opacity))
        }

        .rounded-md {
            border-radius: .375rem;
        }

        .bg-gray-800,
        .bg-gray-800:active {
            --tw-bg-opacity: 1;
            background-color: rgb(31 41 55 / var(--tw-bg-opacity));
        }

        .bg-gray-800:hover {
            --tw-bg-opacity: 1;
            background-color: rgb(55 65 81 / var(--tw-bg-opacity));
        }

        .uppercase {
            text-transform: uppercase;
        }

        .underline {
            text-decoration-line:underline
        }

        .inline-flex {
            display: inline-flex;
        }

        .items-center {
            align-items: center;
        }

        .ml-3 {
            margin-left: 1rem !important;
        }

        .border-gray-300 {
            --tw-border-opacity: 1;
            border-color: rgb(209 213 219 / var(--tw-border-opacity));
        }

        .shadow-sm {
            --tw-shadow: 0 1px 2px 0 rgb(0 0 0 / .05);
            --tw-shadow-colored: 0 1px 2px 0 var(--tw-shadow-color);
            box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000),var(--tw-ring-shadow, 0 0 #0000),var(--tw-shadow);
        }

        .text-indigo-600 {
            --tw-text-opacity: 1;
            color: rgb(79 70 229 / var(--tw-text-opacity));
        }

        .border-gray-300 {
            --tw-border-opacity: 1;
            border-color: rgb(209 213 219 / var(--tw-border-opacity));
        }

        .prel {
            position: relative;
        }

        .shp {
            top: 31px;
            right: 2px;
            cursor: pointer;
            padding: 4px 10px;
            position: absolute;
        }

        button.btn-login {
            color: var(--color-tblue) !important;
            background: white;
            transition: all 300ms ease-in;
            border-color: var(--color-tblue) !important;
        }

        button.btn-login:hover {
            color: white !important;
            background: var(--color-theme);
        }
    </style>
@endsection

@section('page_content')

    <div class="container py-3">
        <div class="row">
            <div class="col-lg-5">
                <div class="login-sideimg">
                    <img class="w-100 h-100" src="{{ asset('assets/public/img/mis-login.jpeg') }}" >
                </div>
            </div>

            <div class="col-lg-7">
                <div class="login-card h-100 d-flex flex-column align-items-center justify-content-center">
                    <div class="login-head text-center mb-3">
                        <h4>Uttarakhand Disaster Preparedness and Resilience Project</h4>
                        <h2 class="mb-0">(U-PREPARE)</h2>
                    </div>

                    <div class="login-body bg-white px-4 py-3">
                        <!-- Session Status -->
                        <x-auth-session-status class="mb-4" :status="session('status')" />

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <!-- Email Address -->
                            <div>
                                <x-input-label for="email" :value="__(' Username')" />
                                {{-- <x-text-input id="email" class="block mt-1 w-full form-control @error('username'){{ __('is-invalid') }}@enderror" type="text" name="username" :value="old('username')" required autofocus autocomplete="username" /> --}}
                                <input id="email" class="block mt-1 w-full form-control @error('username'){{ __('is-invalid') }}@enderror" type="text" name="username" value="{{ old('username') }}" required autofocus autocomplete="username" />
                                {{-- <x-input-error :messages="$errors->get('username')" class="mt-2" /> --}}
                                @error('username')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mt-3 prel">
                                <x-input-label for="password" :value="__('Password')" />
                                {{-- <x-text-input id="password" class="block mt-1 w-full form-control @error('password'){{ __('is-invalid') }}" type="password" name="password" required autocomplete="current-password" /> --}}
                                <i class="shp bi bi-eye-slash"></i>
                                <input id="password" class="block mt-1 w-full form-control @error('password'){{ __('is-invalid') }}@enderror" type="password" name="password" required autocomplete="current-password" />
                                {{-- <x-input-error :messages="$errors->get('password')" class="mt-2" /> --}}
                                @error('password')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Remember Me -->
                            <div class="block mt-3">
                                <label for="remember_me" class="d-inline-flex align-items-center">
                                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                                </label>
                            </div>

                            <div class="d-flex align-items-center justify-content-end mt-4">
                                @if (Route::has('password.request') && false )
                                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                                        {{ __('Forgot your password?') }}
                                    </a>
                                @endif

                                {{--
                                <x-primary-button class="ml-3 btn-login">
                                    {{ __('Log in') }}
                                </x-primary-button>
                                --}}
                                <button class="btn-theme">Log In</button>
                            </div>
                        </form>
                    </div>

                    <div class="login-foot text-center mt-3">
                        <p class="mb-0">Department of Disaster Management & Rehabilitation</p>
                        <p>© {{ date('Y') }}. All Rights Reserved</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('inpage_scripts')
    $('.shp').on('click', function() {
        let $eye = $(this);
        let $inp = $eye.next();
        $eye.toggleClass('bi-eye');

        let $inpt = $eye.hasClass('bi-eye') ? 'text' : 'password';

        $inp.attr('type', $inpt);
    })
@endsection

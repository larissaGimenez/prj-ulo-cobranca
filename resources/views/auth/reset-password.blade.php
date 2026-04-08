<x-guest-layout>
    <div class="row vh-100 g-0">
        <div class="col-lg-6 position-relative d-none d-lg-block">
            <div class="bg-holder" style="background-image:url({{ asset('assets/img/bg/30.png') }});"></div>
        </div>

        <div class="col-lg-6">
            <div class="row flex-center h-100 g-0 px-4 px-sm-0">
                <div class="col col-sm-6 col-lg-7 col-xl-6">
                    <a class="d-flex flex-center text-decoration-none mb-4" href="{{ url('/') }}">
                        <div class="d-flex align-items-center fw-bolder fs-3 d-inline-block">
                            <img src="{{ asset('assets/img/icons/logo.png') }}" alt="logo" width="58" />
                        </div>
                    </a>

                    <div class="text-center">
                        <h4 class="text-body-highlight">{{ __('Forgot your password?') }}</h4>
                        <p class="text-body-tertiary mb-5">
                            {{ __('Enter your email below and we will send you a reset link') }}
                        </p>

                        @if (session('status'))
                            <div class="alert alert-outline-success mb-4" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}"
                            class="d-flex align-items-center mb-5">
                            @csrf

                            <div class="flex-1">
                                <input class="form-control @error('email') is-invalid @enderror" id="email" type="email"
                                    name="email" value="{{ old('email') }}" placeholder="{{ __('Email') }}" required
                                    autofocus />
                                @error('email')
                                    <div class="invalid-feedback text-start">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary ms-2">
                                {{ __('Send') }}<span class="fas fa-chevron-right ms-2"></span>
                            </button>
                        </form>

                        <a class="fs-9 fw-bold" href="{{ route('login') }}">
                            {{ __('Back to Login') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
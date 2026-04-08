@extends('layouts.guest')

{{-- Como o seu layout guest usa {{ $title ?? 'Phoenix' }},
podemos passar o título assim ou via @section --}}
@php $title = 'Login'; @endphp

@section('content')
    <div class="row vh-100 g-0">
        <div class="col-lg-6 position-relative d-none d-lg-block">
            <div class="bg-holder" style="background-image:url({{ asset('assets/img/bg/30.png') }});"></div>
        </div>

        <div class="col-lg-6">
            <div class="row flex-center h-100 g-0 px-4 px-sm-0">
                <div class="col col-sm-6 col-lg-7 col-xl-6">
                    <a class="d-flex flex-center text-decoration-none mb-4" href="/">
                        <div class="d-flex align-items-center fw-bolder fs-3 d-inline-block">
                            <img src="{{ asset('assets/img/icons/logo.png') }}" alt="phoenix" width="58" />
                        </div>
                    </a>

                    <div class="text-center mb-7">
                        <h3 class="text-body-highlight">Entrar</h3>
                        <p class="text-body-tertiary">Acesse sua conta</p>
                    </div>

                    {{-- Alerta de Status (Ex: Senha cadastrada com sucesso) --}}
                    @if (session('status'))
                        <div class="alert alert-outline-success mb-4" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3 text-start">
                            <label class="form-label" for="email">Email</label>
                            <div class="form-icon-container">
                                <input class="form-control form-icon-input @error('email') is-invalid @enderror" id="email"
                                    type="email" name="email" value="{{ old('email') }}" placeholder="nome@exemplo.com.br"
                                    required autofocus />
                                <span class="fas fa-user text-body fs-9 form-icon"></span>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 text-start">
                            <label class="form-label" for="password">Senha</label>
                            <div class="form-icon-container" data-password="data-password">
                                <input class="form-control form-icon-input pe-6 @error('password') is-invalid @enderror"
                                    id="password" type="password" name="password" placeholder="Digite sua senha"
                                    data-password-input="data-password-input" required />
                                <span class="fas fa-key text-body fs-9 form-icon"></span>
                                <button type="button"
                                    class="btn px-3 py-0 h-100 position-absolute top-0 end-0 fs-7 text-body-tertiary"
                                    data-password-toggle="data-password-toggle">
                                    <span class="uil uil-eye show"></span>
                                    <span class="uil uil-eye-slash hide"></span>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row flex-between-center mb-7">
                            <div class="col-auto">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" id="remember_me" type="checkbox" name="remember" />
                                    <label class="form-check-label mb-0" for="remember_me">Lembrar de mim</label>
                                </div>
                            </div>
                            <div class="col-auto">
                                <a class="fs-9 fw-semibold" href="{{ route('password.request') }}">
                                    Esqueci minha senha
                                </a>
                            </div>
                        </div>

                        <button class="btn btn-primary w-100 mb-3" type="submit">Entrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@extends('layouts.guest')

@section('content')
    <div class="container">
        <div class="row flex-center min-vh-100 py-5">
            <div class="col-sm-10 col-md-8 col-lg-5 col-xl-5 col-xxl-3">
                <div class="text-center mb-7">
                    <h3 class="text-body-highlight">Definir Senha</h3>
                    <p class="text-body-tertiary">Olá {{ $user->name }}, escolha uma senha segura para acessar o sistema.
                    </p>
                </div>

                <form method="POST" action="{{ route('password.set_password_update', $user) }}">
                    @csrf
                    <div class="mb-3 text-start">
                        <label class="form-label" for="password">Nova Senha</label>
                        <input class="form-control @error('password') is-invalid @enderror" id="password" name="password"
                            type="password" placeholder="Sua nova senha" required autofocus />
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 text-start">
                        <label class="form-label" for="password_confirmation">Confirmar Senha</label>
                        <input class="form-control" id="password_confirmation" name="password_confirmation" type="password"
                            placeholder="Repita a senha" required />
                    </div>

                    <button class="btn btn-primary w-100 mb-3" type="submit">Finalizar Cadastro</button>
                </form>
            </div>
        </div>
    </div>
@endsection
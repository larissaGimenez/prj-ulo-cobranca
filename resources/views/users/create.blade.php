@extends('layouts.app')

@section('content')
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuários</a></li>
            <li class="breadcrumb-item active">Novo Usuário</li>
        </ol>
    </nav>

    {{-- Note que não passamos a variável $user aqui, o partial lidará com isso --}}
    <form action="{{ route('users.store') }}" method="POST" class="mb-9">
        @csrf

        <div class="row g-3 flex-between-end mb-5">
            <div class="col-auto">
                <h2 class="mb-2">Cadastrar Novo Usuário</h2>
                <h5 class="text-body-tertiary fw-semibold">Preencha os dados abaixo para o acesso ao sistema.</h5>
            </div>
            <div class="col-auto">
                <a href="{{ route('users.index') }}" class="btn btn-phoenix-secondary me-2">Descartar</a>
                <button type="submit" class="btn btn-primary">Salvar Usuário</button>
            </div>
        </div>

        <div class="row g-5">
            <div class="col-12 col-xl-8">
                <div class="card shadow-none border border-300 p-4">
                    <h4 class="mb-3">Informações Pessoais</h4>
                    @include('users.partials._form')
                </div>
            </div>

            {{-- Coluna lateral opcional para dicas ou status rápido --}}
            <div class="col-12 col-xl-4">
                <div class="card shadow-none border border-300 p-4">
                    <h4 class="mb-3">Dica</h4>
                    <p class="text-body-tertiary fs-9">
                        Ao cadastrar um novo usuário, ele receberá um e-mail de confirmação para definir sua senha inicial,
                        caso você não defina uma agora.
                    </p>
                    <hr>
                    <p class="text-body-tertiary fs-9 mb-0">
                        Certifique-se de escolher a <strong>Função</strong> correta para limitar o acesso aos módulos do
                        sistema.
                    </p>
                </div>
            </div>
        </div>
    </form>
@endsection
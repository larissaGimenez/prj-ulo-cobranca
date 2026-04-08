@extends('layouts.app')

@section('content')
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuários</a></li>
            <li class="breadcrumb-item active">Editar: {{ $user->name }}</li>
        </ol>
    </nav>

    <form action="{{ route('users.update', $user) }}" method="POST" class="mb-9">
        @csrf
        @method('PATCH')

        <div class="row g-3 flex-between-end mb-5">
            <div class="col-auto">
                <h2 class="mb-2">Editar Usuário</h2>
                <h5 class="text-body-tertiary fw-semibold">Atualizando informações de {{ $user->name }}</h5>
            </div>
            <div class="col-auto">
                <a href="{{ route('users.index') }}" class="btn btn-phoenix-secondary me-2">Cancelar</a>
                <button type="submit" class="btn btn-primary">Atualizar Cadastro</button>
            </div>
        </div>

        <div class="row g-5">
            <div class="col-12 col-xl-8">
                <div class="card shadow-none border border-300 p-4">
                    @include('users.partials._form')
                </div>
            </div>

            {{-- Coluna Lateral Opcional (Status do Soft Delete) --}}
            <div class="col-12 col-xl-4">
                <div class="card shadow-none border border-300 p-4 bg-light">
                    <h4 class="mb-3">Informações de Auditoria</h4>
                    <p class="text-sm"><strong>Criado em:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
                    <p class="text-sm"><strong>Última atualização:</strong> {{ $user->updated_at->format('d/m/Y H:i') }}</p>
                    @if($user->trashed())
                        <span class="badge badge-phoenix badge-phoenix-danger text-uppercase">Usuário na Lixeira</span>
                    @else
                        <span class="badge badge-phoenix badge-phoenix-success text-uppercase">Cadastro Ativo</span>
                    @endif
                </div>
            </div>
        </div>
    </form>
@endsection
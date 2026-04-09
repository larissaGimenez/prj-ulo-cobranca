@extends('layouts.app')

@section('content')
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuários</a></li>
            <li class="breadcrumb-item active">Editar: {{ $user->name }}</li>
        </ol>
    </nav>

    <form action="{{ route('users.update', $user->id) }}" method="POST" class="mb-9">
        @csrf
        @method('PATCH') {{-- Ou PUT, conforme sua rota --}}

        <div class="row g-3 flex-between-end mb-5">
            <div class="col-auto">
                <h2 class="mb-2">Editar Usuário</h2>
                <h5 class="text-body-tertiary fw-semibold">Atualizando informações de <strong>{{ $user->name }}</strong>
                </h5>
            </div>
            <div class="col-auto">
                <a href="{{ route('users.index') }}" class="btn btn-phoenix-secondary me-2">Cancelar</a>
                <button type="submit" class="btn btn-primary">Atualizar Cadastro</button>
            </div>
        </div>

        <div class="row g-5">
            <div class="col-12 col-xl-8">
                <div class="card shadow-none border border-300 p-4">
                    <h4 class="mb-3">Informações Cadastrais</h4>
                    @include('users.partials._form')
                </div>
            </div>

            {{-- Coluna Lateral de Auditoria --}}
            <div class="col-12 col-xl-4">
                <div class="card shadow-none border border-300 p-4 bg-body-tertiary">
                    <h4 class="mb-3">Auditoria</h4>

                    <div class="mb-3">
                        <label class="form-label d-block mb-1 text-body-quaternary">Data de Criação</label>
                        <p class="fw-semibold text-body-highlight">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label d-block mb-1 text-body-quaternary">Última Modificação</label>
                        <p class="fw-semibold text-body-highlight">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label d-block mb-1 text-body-quaternary">Status no Banco</label>
                        @if($user->trashed())
                            <span class="badge badge-phoenix badge-phoenix-danger">
                                <span class="fas fa-trash me-1"></span>Excluído (Lixeira)
                            </span>
                        @else
                            <span class="badge badge-phoenix badge-phoenix-success">
                                <span class="fas fa-check me-1"></span>Ativo
                            </span>
                        @endif
                    </div>

                    @if($user->invite_sent_at)
                        <div class="mb-0">
                            <label class="form-label d-block mb-1 text-body-quaternary">Convite Enviado em</label>
                            <p class="fw-semibold text-body-highlight">{{ $user->invite_sent_at->format('d/m/Y H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </form>
@endsection
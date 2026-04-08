@extends('layouts.app')

@section('content')
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Usuários</a></li>
            <li class="breadcrumb-item active">{{ $user->name }}</li>
        </ol>
    </nav>

    <div class="row g-5">
        {{-- Coluna Principal: Dados do Usuário --}}
        <div class="col-12 col-xxl-8">
            <div class="mb-5">
                <h2 class="mb-2">{{ $user->name }}</h2>
                <h5 class="text-body-tertiary fw-semibold">Informações de cadastro e histórico</h5>
            </div>

            <div class="card shadow-none border border-300 p-4 mb-4">
                <div class="row g-4">
                    <div class="col-12 col-md-6">
                        <p class="text-sm mb-1 text-body-tertiary fw-bold">NOME COMPLETO</p>
                        <h5 class="text-body-highlight">{{ $user->name }}</h5>
                    </div>
                    <div class="col-12 col-md-6">
                        <p class="text-sm mb-1 text-body-tertiary fw-bold">E-MAIL</p>
                        <h5 class="text-body-highlight">{{ $user->email }}</h5>
                    </div>
                    <div class="col-12 col-md-6">
                        <p class="text-sm mb-1 text-body-tertiary fw-bold">MEMBRO DESDE</p>
                        <h5 class="text-body-highlight">{{ $user->created_at->format('d/m/Y H:i') }}</h5>
                    </div>
                    <div class="col-12 col-md-6">
                        <p class="text-sm mb-1 text-body-tertiary fw-bold">ÚLTIMA ATUALIZAÇÃO</p>
                        <h5 class="text-body-highlight">{{ $user->updated_at->format('d/m/Y H:i') }}</h5>
                    </div>
                </div>
            </div>

            <div class="d-flex">
                <a href="{{ route('users.edit', $user) }}" class="btn btn-phoenix-primary me-2">
                    <span class="fas fa-edit me-2"></span>Editar Perfil
                </a>
                <form action="{{ route('users.destroy', $user) }}" method="POST"
                    onsubmit="return confirm('Deseja desativar este usuário?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-phoenix-danger">
                        <span class="fas fa-trash me-2"></span>Desativar Usuário
                    </button>
                </form>
            </div>
        </div>

        {{-- Sidebar: Status do Convite e Auditoria --}}
        <div class="col-12 col-xxl-4">
            <div class="card shadow-none border border-300 bg-body-tertiary">
                <div class="card-body p-4">
                    <h4 class="mb-4">Status do Acesso</h4>

                    <div class="mb-4">
                        <p class="text-sm mb-1 fw-bold">ESTADO ATUAL:</p>
                        @if($user->trashed())
                            <span class="badge badge-phoenix fs-10 badge-phoenix-secondary">INATIVO (DELETADO)</span>
                        @elseif($user->status === 'pending')
                            <span class="badge badge-phoenix fs-10 badge-phoenix-warning">AGUARDANDO SENHA</span>
                        @else
                            <span class="badge badge-phoenix fs-10 badge-phoenix-success">ACESSO ATIVO</span>
                        @endif
                    </div>

                    @if($user->status === 'pending' && !$user->trashed())
                        <hr class="my-4 border-300" />

                        <div class="mb-3">
                            <p class="text-sm mb-1 fw-bold">ÚLTIMO CONVITE ENVIADO:</p>
                            <p class="text-body-highlight mb-0">
                                <span class="fas fa-clock me-1"></span>
                                {{ $user->invite_sent_at ? $user->invite_sent_at->format('d/m/Y H:i') : 'Nunca enviado' }}
                            </p>
                        </div>

                        <div class="mb-4">
                            <p class="text-sm mb-1 fw-bold">VALIDADE DO LINK:</p>
                            @if($user->isInviteValid())
                                <p class="text-success fw-bold mb-0">
                                    <span class="fas fa-check-circle me-1"></span>Ativo (Expira em breve)
                                </p>
                            @else
                                <p class="text-danger fw-bold mb-0">
                                    <span class="fas fa-exclamation-triangle me-1"></span>Link Expirado
                                </p>
                            @endif
                        </div>

                        <form action="{{ route('users.resend-invite', $user) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">
                                <span class="fas fa-paper-plane me-2"></span>Reenviar Convite
                            </button>
                        </form>
                        <p class="text-center text-body-quaternary fs-10 mt-2">
                            O envio para o n8n será disparado novamente.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@extends('layouts.app')

@section('content')
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Perfis</a></li>
            <li class="breadcrumb-item active text-uppercase">{{ $role->name }}</li>
        </ol>
    </nav>

    <div class="row g-3 flex-between-center mb-5">
        <div class="col-auto">
            <div class="d-flex align-items-center">
                <div class="avatar avatar-xl me-3">
                    <div class="avatar-name rounded-circle bg-primary-subtle text-primary fs-6">
                        <span>{{ strtoupper(substr($role->name, 0, 1)) }}</span>
                    </div>
                </div>
                <div>
                    <h2 class="mb-0 text-uppercase">{{ $role->name }}</h2>
                    <p class="text-body-tertiary mb-0">Guard: <span
                            class="badge badge-phoenix badge-phoenix-warning">{{ $role->guard_name }}</span></p>
                </div>
            </div>
        </div>
        <div class="col-auto">
            <a href="{{ route('roles.edit', $role) }}" class="btn btn-phoenix-primary me-2">
                <span class="fas fa-edit me-2"></span>Editar
            </a>
            <a href="{{ route('roles.index') }}" class="btn btn-link text-body px-0 me-2">Voltar à lista</a>
        </div>
    </div>

    <div class="row g-5">
        <div class="col-12 col-xl-8">
            <div class="card shadow-none border">
                <div class="card-header border-bottom bg-body-highlight">
                    <h4 class="mb-0">Permissões Ativas</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @forelse($role->permissions as $perm)
                            <div class="p-2 border rounded bg-body-emphasis d-flex align-items-center">
                                <span class="fa-solid fa-shield-check text-success me-2"></span>
                                <span class="fw-bold fs-9">{{ $perm->name }}</span>
                            </div>
                        @empty
                            <div class="text-center w-100 py-4">
                                <p class="text-body-tertiary italic">Este perfil não possui permissões atribuídas.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card shadow-none border">
                <div class="card-header border-bottom bg-body-highlight">
                    <h4 class="mb-0">Informações do Sistema</h4>
                </div>
                <div class="card-body">
                    <p class="fs-9 mb-2"><strong>ID:</strong> #{{ $role->id }}</p>
                    <p class="fs-9 mb-2"><strong>Criado em:</strong> {{ $role->created_at->format('d/m/Y H:i') }}</p>
                    <p class="fs-9 mb-0"><strong>Última atualização:</strong> {{ $role->updated_at->format('d/m/Y H:i') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
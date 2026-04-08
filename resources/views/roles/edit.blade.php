@extends('layouts.app')

@section('content')
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Perfis</a></li>
            <li class="breadcrumb-item active">Editar: {{ $role->name }}</li>
        </ol>
    </nav>

    <form action="{{ route('roles.update', $role) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-3 flex-between-center mb-5">
            <div class="col-auto">
                <h2 class="mb-2">Editar Perfil</h2>
                <h5 class="text-body-tertiary fw-semibold">A atualizar as permissões de: <span
                        class="text-primary">{{ $role->name }}</span></h5>
            </div>
            <div class="col-auto">
                <a href="{{ route('roles.index') }}" class="btn btn-phoenix-secondary me-2">Voltar</a>
                <button type="submit" class="btn btn-primary">Guardar Alterações</button>
            </div>
        </div>

        <div class="card shadow-none border">
            <div class="card-body p-4">
                @include('roles.partials._form')
            </div>
        </div>
    </form>
@endsection
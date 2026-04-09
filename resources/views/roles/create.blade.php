@extends('layouts.app')

@section('content')
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Grupos de Usuários</a></li>
            <li class="breadcrumb-item active">Novo Grupo</li>
        </ol>
    </nav>

    <form action="{{ route('roles.store') }}" method="POST">
        @csrf
        <div class="row g-3 flex-between-center mb-5">
            <div class="col-auto">
                <h2 class="mb-2">Cadastrar Novo Grupo</h2>
                <h5 class="text-body-tertiary fw-semibold">Defina o nome e as permissões de acesso para este grupo.</h5>
            </div>
            <div class="col-auto">
                <a href="{{ route('roles.index') }}" class="btn btn-phoenix-secondary me-2">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <span class="fas fa-save me-2"></span>Criar Grupo
                </button>
            </div>
        </div>

        <div class="card shadow-none border border-300" data-component-card="data-component-card">
            <div class="card-body p-4">
                {{-- O partial cuidará de renderizar o nome e os cards de permissões --}}
                @include('roles.partials._form')
            </div>
        </div>
    </form>
@endsection
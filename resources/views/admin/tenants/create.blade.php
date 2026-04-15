@extends('layouts.app')

@section('content')
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.tenants.index') }}">Tenants</a></li>
            <li class="breadcrumb-item active">Configurar Nova Integração</li>
        </ol>
    </nav>

    <div class="mb-9">
        <div class="row g-3 flex-between-center mb-5">
            <div class="col-auto">
                <h2 class="mb-2">Configurar Nova Integração</h2>
                <h5 class="text-body-tertiary fw-semibold">Cadastre as credenciais do novo aplicativo tenant.</h5>
            </div>
        </div>

        <div class="card shadow-none border border-300">
            <div class="card-body p-5">
                <form action="{{ route('admin.tenants.store') }}" method="POST">
                    @include('admin.tenants.partials._form')
                </form>
            </div>
        </div>
    </div>
@endsection
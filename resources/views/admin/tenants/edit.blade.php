@extends('layouts.app')

@section('content')
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.tenants.index') }}">Tenants</a></li>
            <li class="breadcrumb-item active">Editar Integração</li>
        </ol>
    </nav>

    <div class="mb-9">
        <div class="row g-3 flex-between-center mb-5">
            <div class="col-auto">
                <h2 class="mb-2">Editar Integração: {{ $tenant->name }}</h2>
                <h5 class="text-body-tertiary fw-semibold">Atualize as credenciais ou o status de conexão deste app.</h5>
            </div>
        </div>

        <div class="card shadow-none border border-300">
            <div class="card-body p-5">
                <form action="{{ route('admin.tenants.update', $tenant) }}" method="POST">
                    @method('PUT')
                    @include('admin.tenants.partials._form')
                </form>
            </div>
        </div>
    </div>
@endsection
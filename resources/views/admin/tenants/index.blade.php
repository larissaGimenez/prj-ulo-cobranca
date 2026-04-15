@extends('layouts.app')

@section('content')
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Tenants (Aplicativos)</li>
        </ol>
    </nav>

    <div class="mb-9">
        <div class="row g-3 flex-between-center mb-5">
            <div class="col-auto">
                <h2 class="mb-2">Gerenciar Aplicativos</h2>
                <h5 class="text-body-tertiary fw-semibold">Configure as conexões e integrações do sistema.</h5>
            </div>
            <div class="col-auto">
                <a class="btn btn-primary px-5" href="{{ route('admin.tenants.create') }}">
                    <span class="fas fa-plus me-2"></span>Novo Aplicativo
                </a>
            </div>
        </div>

        <div id="tenants-table-container">
            @if(session('success'))
                <div class="alert alert-subtle-success mb-4" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card shadow-none border border-300">
                <div class="card-body p-0">
                    <div class="table-responsive scrollbar">
                        <table class="table table-sm fs-9 mb-0">
                            <thead>
                                <tr>
                                    <th class="white-space-nowrap align-middle ps-4" style="width:20px;">#</th>
                                    <th class="white-space-nowrap align-middle">Nome do Aplicativo</th>
                                    <th class="white-space-nowrap align-middle">Sistema</th>
                                    <th class="white-space-nowrap align-middle text-center">Status</th>
                                    <th class="white-space-nowrap align-middle">Criado em</th>
                                    <th class="white-space-nowrap align-middle text-end pe-4">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                @forelse($tenants as $tenant)
                                    <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                                        <td class="align-middle white-space-nowrap ps-4 fw-bold text-body-tertiary">
                                            {{ $tenant->id }}
                                        </td>
                                        <td class="align-middle white-space-nowrap">
                                            <a class="fw-bold text-body-highlight pe-3" href="{{ route('admin.tenants.show', $tenant) }}">
                                                {{ $tenant->name }}
                                            </a>
                                        </td>
                                        <td class="align-middle white-space-nowrap text-uppercase fw-semibold text-body-tertiary">
                                            {{ $tenant->driver }}
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge badge-phoenix fs-10 badge-phoenix-{{ $tenant->is_active ? 'success' : 'danger' }}">
                                                {{ $tenant->is_active ? 'Ativo' : 'Inativo' }}
                                            </span>
                                        </td>
                                        <td class="align-middle white-space-nowrap text-body-tertiary">
                                            {{ $tenant->created_at->format('d/m/Y') }}
                                        </td>
                                        <td class="align-middle text-end white-space-nowrap pe-4">
                                            <div class="font-sans-serif btn-reveal-trigger position-static">
                                                <button class="btn btn-sm dropdown-toggle dropdown-caret-none transition-none btn-reveal fs-10" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false">
                                                    <span class="fas fa-ellipsis-h fs-10"></span>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end py-2">
                                                    <a class="dropdown-item" href="{{ route('admin.tenants.show', $tenant) }}">Visualizar</a>
                                                    <a class="dropdown-item" href="{{ route('admin.tenants.edit', $tenant) }}">Editar</a>
                                                    <div class="dropdown-divider"></div>
                                                    <form action="{{ route('admin.tenants.destroy', $tenant) }}" method="POST" onsubmit="return confirm('Deseja realmente remover este aplicativo?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">Excluir</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">Nenhum aplicativo cadastrado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between py-2">
                    <div class="mt-1">
                        {{ $tenants->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
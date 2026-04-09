@extends('layouts.app')

@section('content')
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Grupos de Usuários</li>
        </ol>
    </nav>

    <h2 class="text-bold text-body-emphasis mb-5">Grupos de Usuários</h2>

    <div id="roles-table-container"
        data-list='{"valueNames":["role_name","permissions_count","created_at"],"page":10,"pagination":true}'>

        <div class="row align-items-center justify-content-between g-3 mb-4">
            <div class="col col-auto">
                <div class="search-box">
                    <form class="position-relative">
                        <input class="form-control search-input search" type="search" placeholder="Buscar perfis"
                            aria-label="Search" />
                        <span class="fas fa-search search-box-icon"></span>
                    </form>
                </div>
            </div>
            <div class="col-auto">
                <div class="d-flex align-items-center">
                    <button class="btn btn-link text-body me-4 px-0">
                        <span class="fa-solid fa-file-export fs-9 me-2"></span>Exportar
                    </button>
                    <a href="{{ route('roles.create') }}" class="btn btn-primary">
                        <span class="fas fa-plus me-2"></span>Novo Grupo
                    </a>
                </div>
            </div>
        </div>

        <div class="mx-n4 mx-lg-n6 px-4 px-lg-6 mb-9 bg-body-emphasis border-y mt-2 position-relative top-1">
            <div class="table-responsive scrollbar ms-n1 ps-1">
                <table class="table table-sm fs-9 mb-0">
                    <thead>
                        <tr>
                            <th class="white-space-nowrap fs-9 align-middle ps-0" style="max-width:20px;">
                                <div class="form-check mb-0 fs-8">
                                    <input class="form-check-input" id="checkbox-bulk-roles-select" type="checkbox"
                                        data-bulk-select='{"body":"roles-table-body"}' />
                                </div>
                            </th>
                            <th class="sort align-middle" scope="col" data-sort="role_name" style="width:25%;">NOME DO
                                PERFIL</th>
                            <th class="sort align-middle" scope="col" data-sort="permissions_count" style="width:35%;">
                                PERMISSÕES ATRIBUÍDAS</th>
                            <th class="sort align-middle text-end" scope="col" data-sort="created_at">CRIADO EM</th>
                            <th class="sort align-middle text-end pe-0" scope="col">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody class="list" id="roles-table-body">
                        @foreach($roles as $role)
                            <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                                <td class="fs-9 align-middle ps-0 py-3">
                                    <div class="form-check mb-0 fs-8">
                                        <input class="form-check-input" type="checkbox"
                                            data-bulk-select-row='{"id":{{ $role->id }}}' />
                                    </div>
                                </td>
                                <td class="role_name align-middle white-space-nowrap">
                                    <a class="d-flex align-items-center text-body text-hover-1000"
                                        href="{{ route('roles.show', $role) }}">
                                        <div class="avatar avatar-m">
                                            <div class="avatar-name rounded-circle bg-primary-subtle text-primary">
                                                <span>{{ strtoupper(substr($role->name, 0, 1)) }}</span>
                                            </div>
                                        </div>
                                        <h6 class="mb-0 ms-3 fw-semibold">{{ $role->name }}</h6>
                                    </a>
                                </td>
                                <td class="permissions_count align-middle">
                                    @php $perms = $role->permissions->take(3); @endphp
                                    @foreach($perms as $perm)
                                        <span class="badge badge-phoenix fs-10 badge-phoenix-info me-1">{{ $perm->name }}</span>
                                    @endforeach
                                    @if($role->permissions->count() > 3)
                                        <span class="text-body-tertiary fs-10 fw-bold">+{{ $role->permissions->count() - 3 }}</span>
                                    @endif
                                </td>

                                <td class="created_at align-middle text-end white-space-nowrap text-body-tertiary">
                                    {{ $role->created_at->format('d/m/Y H:i') }}
                                </td>

                                <td class="align-middle text-end white-space-nowrap pe-0">
                                    <div class="font-sans-serif btn-reveal-trigger position-static">
                                        <button
                                            class="btn btn-sm dropdown-toggle dropdown-caret-none transition-none btn-reveal"
                                            type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="fas fa-ellipsis-h fs-10"></span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end py-2">
                                            <a class="dropdown-item" href="{{ route('roles.show', $role) }}">Visualizar</a>
                                            <a class="dropdown-item" href="{{ route('roles.edit', $role) }}">Editar</a>
                                            <div class="dropdown-divider"></div>
                                            <form action="{{ route('roles.destroy', $role) }}" method="POST"
                                                onsubmit="return confirm('Deseja excluir permanentemente este perfil?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">Excluir</button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="row align-items-center justify-content-between py-2 pe-0 fs-9">
                <div class="col-auto d-flex">
                    {{-- Caso você esteja usando paginação no Controller --}}
                    @if(method_exists($roles, 'links'))
                        {{ $roles->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
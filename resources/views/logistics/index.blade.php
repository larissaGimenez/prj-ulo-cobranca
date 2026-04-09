@extends('layouts.app')

@section('content')
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Logística</li>
        </ol>
    </nav>

    <h2 class="text-bold text-body-emphasis mb-5">Gestão de Logística</h2>

    <div id="logistics-table-container" data-list='{"valueNames":["name","created_at"],"page":10,"pagination":true}'>
        <div class="row align-items-center justify-content-between g-3 mb-4">
            <div class="col col-auto">
                <div class="search-box">
                    <form class="position-relative">
                        <input class="form-control search-input search" type="search" placeholder="Buscar carga ou rota"
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
                    <a href="#" class="btn btn-primary">
                        <span class="fas fa-plus me-2"></span>Nova Operação
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
                                    <input class="form-check-input" id="checkbox-bulk-logistics-select" type="checkbox"
                                        data-bulk-select='{"body":"logistics-table-body"}' />
                                </div>
                            </th>
                            <th class="sort align-middle" scope="col" data-sort="name" style="width:70%;">DESCRIÇÃO / ROTA
                            </th>
                            <th class="sort align-middle text-end" scope="col" data-sort="created_at">CRIADO EM</th>
                            <th class="sort align-middle text-end pe-0" scope="col">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody class="list" id="logistics-table-body">
                        @foreach($logistics as $item)
                            <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                                <td class="fs-9 align-middle ps-0 py-3">
                                    <div class="form-check mb-0 fs-8">
                                        <input class="form-check-input" type="checkbox"
                                            data-bulk-select-row='{"id":{{ $item->id }}}' />
                                    </div>
                                </td>
                                <td class="name align-middle white-space-nowrap">
                                    <a class="d-flex align-items-center text-body text-hover-1000" href="#">
                                        <h6 class="mb-0 fw-semibold">{{ $item->name }}</h6>
                                    </a>
                                </td>
                                <td class="created_at align-middle text-end white-space-nowrap text-body-tertiary">
                                    {{ $item->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="align-middle text-end white-space-nowrap pe-0">
                                    <div class="font-sans-serif btn-reveal-trigger position-static">
                                        <button
                                            class="btn btn-sm dropdown-toggle dropdown-caret-none transition-none btn-reveal"
                                            type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="fas fa-ellipsis-h fs-10"></span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end py-2">
                                            <a class="dropdown-item" href="#">Visualizar</a>
                                            <a class="dropdown-item" href="#">Editar</a>
                                            <div class="dropdown-divider"></div>
                                            <form action="#" method="POST">
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
                    {{ $logistics->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
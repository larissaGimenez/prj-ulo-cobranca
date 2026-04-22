@extends('layouts.app')

@section('content')
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Negociações</li>
        </ol>
    </nav>

    <div class="mb-5">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="mb-2">Negociações</h2>
                <h5 class="text-body-tertiary fw-semibold">Gerencie todos os acordos e propostas.</h5>
            </div>
            <div class="col-auto">
                <a href="{{ route('negotiations.create') }}" class="btn btn-primary">
                    <span class="fas fa-plus me-2"></span>Nova Negociação
                </a>
            </div>
        </div>
    </div>

    <div class="card shadow-none border border-300">
        <div class="card-body p-0">
            <div class="table-responsive scrollbar">
                <table class="table table-sm fs-9 mb-0">
                    <thead>
                        <tr>
                            <th class="white-space-nowrap align-middle ps-4">CLIENTE</th>
                            <th class="white-space-nowrap align-middle">STATUS</th>
                            <th class="white-space-nowrap align-middle">VALOR ACORDO</th>
                            <th class="white-space-nowrap align-middle">PARCELAS</th>
                            <th class="white-space-nowrap align-middle">CRIADO EM</th>
                            <th class="white-space-nowrap align-middle text-end pe-4">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody class="list">
                        @foreach($negotiations as $negotiation)
                            <tr class="align-middle">
                                <td class="ps-4">
                                    <a class="fw-bold" href="{{ route('billings.show', $negotiation->operation->cliente->id ?? '') }}">
                                        {{ $negotiation->operation->cliente->nome ?? 'N/A' }}
                                    </a>
                                </td>
                                <td>
                                    @php
                                        $statusClass = match($negotiation->status) {
                                            'em andamento' => 'badge-phoenix-warning',
                                            'quitado' => 'badge-phoenix-success',
                                            'cancelado' => 'badge-phoenix-danger',
                                            default => 'badge-phoenix-secondary'
                                        };
                                    @endphp
                                    <span class="badge badge-phoenix fs-10 {{ $statusClass }}">
                                        {{ strtoupper($negotiation->status) }}
                                    </span>
                                </td>
                                <td>R$ {{ number_format($negotiation->details['valor_proposta'] ?? 0, 2, ',', '.') }}</td>
                                <td>{{ $negotiation->details['numero_parcelas'] ?? 0 }}x</td>
                                <td>{{ $negotiation->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-end pe-4">
                                    <div class="btn-reveal-trigger">
                                        <button class="btn btn-sm dropdown-toggle dropdown-caret-none transition-none btn-reveal" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="fas fa-ellipsis-h"></span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end py-2">
                                            <a class="dropdown-item" href="{{ route('negotiations.show', $negotiation) }}">Visualizar</a>
                                            <a class="dropdown-item" href="{{ route('negotiations.edit', $negotiation) }}">Editar</a>
                                            <div class="dropdown-divider"></div>
                                            <form action="{{ route('negotiations.destroy', $negotiation) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta negociação?')">
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
            <div class="p-3">
                {{ $negotiations->links() }}
            </div>
        </div>
    </div>
@endsection

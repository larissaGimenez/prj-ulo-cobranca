@extends('layouts.app')

@section('content')
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('negotiations.index') }}">Negociações</a></li>
            <li class="breadcrumb-item active">Detalhes</li>
        </ol>
    </nav>

    <div class="mb-5">
        <h2 class="mb-2">Detalhes da Negociação #{{ $negotiation->id }}</h2>
        <h5 class="text-body-tertiary fw-semibold">Informações detalhadas do acordo e parcelamento.</h5>
    </div>

    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <div class="card shadow-none border border-300 mb-4">
                <div class="card-header border-bottom border-300">
                    <h4 class="mb-0">Informações Gerais</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <p class="text-body-tertiary fw-bold fs-10 mb-1">CLIENTE</p>
                            <h5 class="mb-0">{{ $negotiation->operation->cliente->nome ?? 'N/A' }}</h5>
                        </div>
                        <div class="col-12 col-sm-6">
                            <p class="text-body-tertiary fw-bold fs-10 mb-1">STATUS</p>
                            <span class="badge badge-phoenix badge-phoenix-primary">{{ strtoupper($negotiation->status) }}</span>
                        </div>
                        <div class="col-12 col-sm-6">
                            <p class="text-body-tertiary fw-bold fs-10 mb-1">VALOR TOTAL ACORDADO</p>
                            <h5 class="mb-0">R$ {{ number_format($negotiation->details['valor_proposta'] ?? 0, 2, ',', '.') }}</h5>
                        </div>
                        <div class="col-12 col-sm-6">
                            <p class="text-body-tertiary fw-bold fs-10 mb-1">VALOR ENTRADA</p>
                            <h5 class="mb-0">R$ {{ number_format($negotiation->details['valor_entrada'] ?? 0, 2, ',', '.') }}</h5>
                        </div>
                        <div class="col-12">
                            <p class="text-body-tertiary fw-bold fs-10 mb-1">OBSERVAÇÕES</p>
                            <p class="mb-0">{{ $negotiation->details['observacoes'] ?? 'Sem observações.' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-none border border-300 mb-4">
                <div class="card-header border-bottom border-300">
                    <h4 class="mb-0">Cronograma de Pagamentos</h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm fs-9 mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">PARCELA</th>
                                    <th>VENCIMENTO</th>
                                    <th class="text-end pe-4">VALOR</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($negotiation->details['parcelas']))
                                    @foreach($negotiation->details['parcelas'] as $parcela)
                                        <tr>
                                            <td class="ps-4">Parcela {{ $parcela['id'] }}</td>
                                            <td>{{ isset($parcela['vencimento']) ? \Carbon\Carbon::parse($parcela['vencimento'])->format('d/m/Y') : '-' }}</td>
                                            <td class="text-end pe-4">R$ {{ number_format($parcela['valor'] ?? 0, 2, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-body-tertiary">Nenhuma parcela detalhada.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card shadow-none border border-300 position-sticky" style="top: 80px">
                <div class="card-body">
                    <h4 class="mb-3">Ações</h4>
                    <div class="d-grid gap-2">
                        <a href="{{ route('negotiations.edit', $negotiation) }}" class="btn btn-phoenix-primary">
                            <span class="fas fa-edit me-2"></span>Editar Negociação
                        </a>
                        <form action="{{ route('negotiations.destroy', $negotiation) }}" method="POST" onsubmit="return confirm('Tem certeza?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-phoenix-danger w-100">
                                <span class="fas fa-trash me-2"></span>Excluir
                            </button>
                        </form>
                        <a href="{{ route('negotiations.index') }}" class="btn btn-phoenix-secondary">
                            <span class="fas fa-chevron-left me-2"></span>Voltar para Lista
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

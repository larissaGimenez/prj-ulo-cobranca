@extends('layouts.app')

@section('content')
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Central de Cobrança</li>
        </ol>
    </nav>

    <div class="row g-3 flex-between-center mb-5">
        <div class="col-auto">
            <h2 class="mb-2">Painel de Cobrança</h2>
            <h5 class="text-body-tertiary fw-semibold">Visualização unificada de títulos e negociações do cliente.</h5>
        </div>
        <div class="col-auto">
            <button class="btn btn-phoenix-secondary me-2">
                <span class="fas fa-file-export me-2"></span>Exportar Relatório
            </button>
            @can('criar-cobrancas')
                <button class="btn btn-primary">
                    <span class="fas fa-plus me-2"></span>Nova Cobrança
                </button>
            @endcan
        </div>
    </div>

    <div class="row g-4">
        
        <div class="col-12">
            <div class="card shadow-none border border-300" data-component-card="data-component-card">
                <div class="card-header bg-body-tertiary py-2 px-3">
                    <h6 class="mb-0 text-uppercase fs-10 text-body-highlight fw-bold">
                        <span class="fas fa-user-tie me-2 text-primary"></span>
                        Ficha Cadastral do Cliente
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 align-items-center">
                        <div class="col-auto">
                            <div class="avatar avatar-4xl rounded-circle border border-2 border-primary-100">
                                <div class="avatar-name rounded-circle text-primary bg-primary-subtle fs-5">
                                    <span>MS</span>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <h3 class="mb-1 text-bold">Larissa Manzano</h3>
                            <p class="text-body-tertiary fs-9 mb-1">
                                <span class="fas fa-envelope me-2"></span>larissa@material.com.br
                            </p>
                            <p class="text-body-tertiary fs-9 mb-0">
                                <span class="fas fa-phone me-2"></span>(19) 98765-4321
                            </p>
                        </div>
                        <div class="col-md-auto text-md-end border-start border-300 ms-md-4 ps-md-4">
                            <p class="text-body-tertiary fs-10 mb-1 text-uppercase fw-bold">Risco de Crédito</p>
                            <h1 class="mb-0 text-warning">Score: B</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-7">
            <div class="card shadow-none border border-300 h-100" data-component-card="data-component-card">
                <div class="card-header bg-body-tertiary py-2 px-3 flex-between-center">
                    <h6 class="mb-0 text-uppercase fs-10 text-body-highlight fw-bold">
                        <span class="fas fa-money-bill-wave me-2 text-success"></span>
                        Títulos em Aberto e Vencidos
                    </h6>
                    <span class="badge badge-phoenix badge-phoenix-danger fs-10">4 Vencidos</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive scrollbar">
                        <table class="table table-sm fs-9 mb-0">
                            <thead>
                                <tr>
                                    <th class="white-space-nowrap align-middle ps-3">Vencimento</th>
                                    <th class="white-space-nowrap align-middle">Documento</th>
                                    <th class="white-space-nowrap align-middle text-end">Valor R$</th>
                                    <th class="white-space-nowrap align-middle text-end pe-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $titulos = [
                                    ['05/04/2026', 'NF-12345', '1.250,00', 'danger', 'Vencido'],
                                    ['15/04/2026', 'NF-12346', '3.890,50', 'danger', 'Vencido'],
                                    ['10/05/2026', 'NF-12400', '950,00', 'warning', 'A Vencer'],
                                    ['05/06/2026', 'NF-12510', '2.100,00', 'warning', 'A Vencer'],
                                ]; @endphp
                                @foreach($titulos as $titulo)
                                    <tr class="align-middle">
                                        <td class="ps-3">{{ $titulo[0] }}</td>
                                        <td>{{ $titulo[1] }}</td>
                                        <td class="text-end fw-bold">{{ $titulo[2] }}</td>
                                        <td class="text-end pe-3">
                                            <span class="badge badge-phoenix fs-10 badge-phoenix-{{ $titulo[3] }}">{{ $titulo[4] }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-5">
            <div class="card shadow-none border border-300 h-100" data-component-card="data-component-card">
                <div class="card-header bg-body-tertiary py-2 px-3 flex-between-center">
                    <h6 class="mb-0 text-uppercase fs-10 text-body-highlight fw-bold">
                        <span class="fas fa-handshake me-2 text-warning"></span>
                        Histórico de Negociações
                    </h6>
                    <button class="btn btn-link btn-sm text-body me-n3 px-0">Ver Todas</button>
                </div>
                <div class="card-body p-3">
                    @php $negociacoes = [
                        ['danger', 'Acordo quebrado pelo cliente', 'Vencimento 10/04/2026', 'R$ 2.500,00'],
                        ['success', 'Acordo firmado e pago', 'Pago em 01/03/2026', 'R$ 5.140,00'],
                        ['warning', 'Em negociação (Aguarda aceite)', 'Proposta enviada hoje', 'R$ 950,00'],
                    ]; @endphp
                    @foreach($negociacoes as $negoc)
                        <div class="d-flex align-items-start mb-3 border-bottom border-200 pb-3">
                            <span class="fa-solid fa-circle text-{{ $negoc[0] }} fs-10 mt-1"></span>
                            <div class="ms-3 flex-1">
                                <h6 class="mb-1 text-bold">{{ $negoc[1] }}</h6>
                                <p class="text-body-tertiary fs-9 mb-0">Status: {{ $negoc[2] }}</p>
                                <p class="text-body-highlight fs-9 mb-0 fw-bold">Valor Acordo: {{ $negoc[3] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card shadow-none border border-300" data-component-card="data-component-card">
                <div class="card-header bg-body-tertiary py-2 px-3">
                    <h6 class="mb-0 text-uppercase fs-10 text-body-highlight fw-bold">
                        <span class="fas fa-comments me-2 text-info"></span>
                        Histórico de Interações / Comentários
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="p-3 border-bottom border-200 bg-light">
                        <label class="form-label fs-9" for="novo_comentario">Adicionar Comentário Rápido</label>
                        <div class="input-group">
                            <input class="form-control" id="novo_comentario" type="text" placeholder="Digite sua observação sobre a cobrança..." />
                            <button class="btn btn-phoenix-primary" type="button">Postar</button>
                        </div>
                    </div>
                    <div class="p-4">
                        @php $comentarios = [
                            ['Admin', 'MS', 'primary', 'primary-subtle', '09/04/2026 14:30', 'Cliente alega que não recebeu o boleto bancário por e-mail. Reenviado via WhatsApp.'],
                            ['Larissa Manzano (Cliente)', 'LM', 'warning', 'warning-subtle', '08/04/2026 11:15', 'Registrou promessa de pagamento para 10/04/2026 na central do cliente.'],
                            ['Sistema', 'SI', 'tertiary', 'tertiary-subtle', '01/04/2026 09:00', 'Webhook n8n: E-mail de lembrete de vencimento enviado com sucesso.'],
                        ]; @endphp
                        @foreach($comentarios as $coment)
                            <div class="d-flex align-items-start mb-4">
                                <div class="avatar avatar-m rounded-circle">
                                    <div class="avatar-name rounded-circle text-{{ $coment[2] }} bg-{{ $coment[3] }}">
                                        <span>{{ $coment[1] }}</span>
                                    </div>
                                </div>
                                <div class="ms-3 flex-1 bg-light p-3 rounded-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0 text-bold">{{ $coment[0] }}</h6>
                                        <p class="text-body-tertiary fs-10 mb-0">{{ $coment[4] }}</p>
                                    </div>
                                    <p class="text-body-highlight fs-9 mb-0">{{ $coment[5] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
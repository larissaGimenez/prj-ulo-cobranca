@extends('layouts.app')

@section('content')
    <style>
        .table-scroll-limit {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>

    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('billings.index') }}">Central de Cobrança</a></li>
            <li class="breadcrumb-item active">Ficha do Cliente</li>
        </ol>
    </nav>

    <div class="row g-3 flex-between-center mb-5">
        <div class="col-auto">
            <h2 class="mb-2">Painel de Cobrança</h2>
            <h5 class="text-body-tertiary fw-semibold">Visualização unificada de títulos e negociações de:
                <strong>{{ $cliente->nome }}</strong>
            </h5>
        </div>
    </div>

    <div class="row g-4">
        {{-- FICHA CADASTRAL --}}
        <div class="col-12">
            <div class="card shadow-none border border-300">
                <div class="card-body p-4">
                    <div class="row g-3 align-items-center">
                        <div class="col-auto">
                            <div class="avatar avatar-4xl rounded-circle border border-2 border-primary-100">
                                <div class="avatar-name rounded-circle text-primary bg-primary-subtle fs-5">
                                    <span>{{ substr($cliente->nome, 0, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <h3 class="mb-1 text-bold">{{ $cliente->nome }}</h3>
                            <p class="text-body-tertiary fs-9 mb-1">
                                <span class="fas fa-building me-2"></span>Empresa:
                                {{ $cliente->empresa ?? 'Não Informada' }}
                            </p>
                            <p class="text-body-tertiary fs-9 mb-0"><span class="fas fa-id-card me-2"></span>CPF/CNPJ:
                                {{ $cliente->cpf_cnpj ?? '---' }}
                            </p>
                        </div>

                        {{-- ÁREA DE SCORE E TOTAL --}}
                        <div class="col-md-auto ms-md-4 ps-md-4">
                            <div class="d-flex align-items-center">
                                {{-- Total Vencido --}}
                                <div class="pe-4 border-end border-300">
                                    <p class="text-body-tertiary fs-10 mb-1 text-uppercase fw-bold">Total Vencido</p>
                                    <h1 class="mb-0 text-danger fw-bold">R$ {{ number_format($totalDivida, 2, ',', '.') }}
                                    </h1>
                                </div>

                                {{-- Risco de Crédito --}}
                                <div class="px-4 border-end border-300">
                                    <p class="text-body-tertiary fs-10 mb-1 text-uppercase fw-bold">Risco de Crédito</p>
                                    <h1 class="mb-0 text-warning fw-bold">Score: B</h1>
                                </div>

                                {{-- Dias em Atraso --}}
                                <div class="ps-4">
                                    <p class="text-body-tertiary fs-10 mb-1 text-uppercase fw-bold">Dias em Atraso</p>
                                    <h1 class="mb-0 text-dark fw-bold">{{ $diasAtraso }} <small class="fs-9 fw-normal">dias</small></h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TÍTULOS COM SCROLL --}}
        <div class="col-12 col-xl-7">
            <div class="card shadow-none border border-300 h-100">
                <div class="card-header bg-body-tertiary py-2 px-3 flex-between-center">
                    <h6 class="mb-0 text-uppercase fs-10 text-body-highlight fw-bold">
                        <span class="fas fa-money-bill-wave me-2 text-success"></span>
                        Títulos em Aberto e Vencidos
                    </h6>
                    <span class="badge badge-phoenix badge-phoenix-danger fs-10">{{ $vencidosCount }} Vencidos</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive scrollbar table-scroll-limit"> {{-- Aplicado scroll aqui --}}
                        <table class="table table-sm fs-9 mb-0">
                            <thead class="bg-body-emphasis position-sticky top-0" style="z-index: 1;">
                                <tr>
                                    <th class="white-space-nowrap align-middle ps-3">Vencimento</th>
                                    <th class="white-space-nowrap align-middle">Parcela</th>
                                    <th class="white-space-nowrap align-middle">Documento</th>
                                    <th class="white-space-nowrap align-middle text-end">Valor R$</th>
                                    <th class="white-space-nowrap align-middle text-end pe-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                @forelse($titulos as $titulo)
                                    @php
                                        $vencimento = \Carbon\Carbon::createFromFormat('d/m/Y', $titulo->data_venc);
                                        $hoje = \Carbon\Carbon::today();

                                        // Lógica de Status
                                        $statusReal = strtoupper($titulo->status);
                                        $isPago = in_array($statusReal, ['PAGO', 'LIQUIDADO', 'RECEBIDO']);
                                        $isVencido = $vencimento->isBefore($hoje) && !$isPago;
                                    @endphp
                                    <tr class="align-middle">
                                        <td class="ps-3">{{ $titulo->data_venc }}</td>
                                        <td class="text-body-tertiary fw-bold">{{ $titulo->numero_parcela }}</td>
                                        <td>{{ $titulo->cod_lanc }}</td>
                                        <td class="text-end fw-bold">
                                            R$ {{ number_format($titulo->valor_float, 2, ',', '.') }}
                                        </td>
                                        <td class="text-end pe-3">
                                            @if($isPago)
                                                <span class="badge badge-phoenix fs-10 badge-phoenix-success">
                                                    <span class="fas fa-check me-1"></span>Pago
                                                </span>
                                            @elseif($isVencido)
                                                <span class="badge badge-phoenix fs-10 badge-phoenix-danger">
                                                    Vencido
                                                </span>
                                            @else
                                                <span class="badge badge-phoenix fs-10 badge-phoenix-warning">
                                                    A Vencer
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4">Nenhum título encontrado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- HISTÓRICO DE NEGOCIAÇÕES --}}
        <div class="col-12 col-xl-5">
            <div class="card shadow-none border border-300 h-100">
                <div class="card-header bg-body-tertiary py-2 px-3 d-flex justify-content-between">
                    <h6 class="mb-0 text-uppercase fs-10 text-body-highlight fw-bold">
                        <span class="fas fa-handshake me-2 text-warning"></span>
                        Histórico de Negociações
                    </h6>
                    <a href="{{ route('negotiations.create', ['operation_id' => $operation->id ?? '']) }}" class="btn btn-phoenix-primary btn-sm">
                        <span class="fas fa-plus me-1"></span>Novo Acordo
                    </a>
                </div>
                <div class="card-body p-3">
                    @if($operation && $operation->negotiations->count() > 0)
                        @foreach($operation->negotiations as $neg)
                            <div class="d-flex align-items-center mb-3 border-bottom border-200 pb-3">
                                <div class="flex-1">
                                    <h6 class="mb-1 text-bold">
                                        <a href="{{ route('negotiations.show', $neg) }}">Acordo #{{ $neg->id }}</a>
                                        @php
                                            $statusClass = match($neg->status) {
                                                'em andamento' => 'text-warning',
                                                'quitado' => 'text-success',
                                                'cancelado' => 'text-danger',
                                                default => 'text-secondary'
                                            };
                                        @endphp
                                        <span class="{{ $statusClass }} fs-10 ms-2">• {{ strtoupper($neg->status) }}</span>
                                    </h6>
                                    <p class="text-body-tertiary fs-9 mb-0">
                                        Valor: R$ {{ number_format($neg->details['valor_proposta'] ?? 0, 2, ',', '.') }} | 
                                        {{ $neg->created_at->format('d/m/Y') }}
                                    </p>
                                </div>
                                <div class="col-auto">
                                    <a href="{{ route('negotiations.edit', $neg) }}" class="btn btn-link p-0 text-body-tertiary">
                                        <span class="fas fa-edit fs-10"></span>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="d-flex align-items-start mb-3 border-bottom border-200 pb-3">
                            <span class="fa-solid fa-circle text-info fs-10 mt-1"></span>
                            <div class="ms-3 flex-1">
                                <h6 class="mb-1 text-bold">Nenhum acordo ativo</h6>
                                <p class="text-body-tertiary fs-9 mb-0">Use o botão acima para registrar uma proposta.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- HISTÓRICO DE CHECKLIST --}}
        <div class="col-12 mb-4">
            <div class="card shadow-none border border-300">
                <div class="card-header bg-body-tertiary py-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 text-uppercase fs-10 text-body-highlight fw-bold">
                        <span class="fas fa-check-double me-2 text-success"></span>
                        Checklist de Evolução da Cobrança
                    </h6>
                    <button class="btn btn-link btn-sm p-0 fs-10 fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#addCustomTaskCollapse">
                        <i class="fas fa-plus-circle me-1"></i>ADICIONAR TAREFA
                    </button>
                </div>
                
                <div class="collapse" id="addCustomTaskCollapse">
                    <div class="bg-body-highlight border-bottom border-translucent p-4">
                        <form action="{{ route('billings.add_checklist_item', $operation->id) }}" method="POST" class="row g-3 align-items-end">
                            @csrf
                            <div class="col-md-6">
                                <label class="form-label fs-10 fw-bold text-uppercase">Descrição da Tarefa</label>
                                <input type="text" name="text" class="form-control" placeholder="Ex: Aguardando retorno do sócio..." required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fs-10 fw-bold text-uppercase">Vincular à Etapa</label>
                                <select name="stage_id" class="form-select" required>
                                    @foreach($stages as $stg)
                                        <option value="{{ $stg->id }}" {{ $operation->billing_kanban_stage_id == $stg->id ? 'selected' : '' }}>{{ $stg->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100 fw-bold shadow-none">ADICIONAR</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm fs-9 mb-0">
                            <thead>
                                <tr class="bg-body-emphasis">
                                    <th class="ps-3" style="width: 70%">Tarefa</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($operation && !empty($operation->checklist_data))
                                    @foreach($operation->checklist_data as $index => $item)
                                        <tr class="checklist-row-item">
                                            <td class="ps-3 py-3 align-middle">
                                                <div class="d-flex align-items-center">
                                                    <span class="badge badge-phoenix fs-11 me-2 {{ $item['completed'] ? 'badge-phoenix-success' : 'badge-phoenix-secondary' }}">
                                                        <i class="fas {{ $item['completed'] ? 'fa-check' : 'fa-clock' }}"></i>
                                                    </span>
                                                    <span class="{{ $item['completed'] ? 'text-decoration-line-through text-body-tertiary' : 'text-body-highlight' }} fs-9 fw-bold">
                                                        {{ $item['text'] }}
                                                        @if($item['is_custom'] ?? false)
                                                            <small class="badge badge-phoenix badge-phoenix-info ms-2 fs-11">PERSONALIZADO</small>
                                                        @endif
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="text-center align-middle">
                                                <div class="d-flex justify-content-center gap-2">
                                                    @if($item['completed'])
                                                        <span class="badge badge-phoenix badge-phoenix-success fs-10">CONCLUÍDO</span>
                                                    @else
                                                        <span class="badge badge-phoenix badge-phoenix-warning fs-10">PENDENTE</span>
                                                    @endif
                                                    
                                                    <button class="btn btn-link p-0 text-danger" type="button" onclick="removeItemFromChecklist({{ $index }}, this)">
                                                        <i class="fas fa-trash-alt fs-11"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="2" class="text-center py-4 text-body-tertiary italic">Nenhum checklist registrado para esta operação.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- COMENTÁRIOS --}}
        <div class="col-12 mb-4">
            <div class="card shadow-none border border-300">
                <div class="card-header bg-body-tertiary py-2 px-3">
                    <h6 class="mb-0 text-uppercase fs-10 text-body-highlight fw-bold">
                        <span class="fas fa-comments me-2 text-info"></span>
                        Histórico de Interações
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="p-3 border-bottom border-200 bg-light">
                        <div class="input-group">
                            <input class="form-control" type="text" placeholder="Adicionar observação..." />
                            <button class="btn btn-phoenix-primary" type="button">Postar</button>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="d-flex align-items-start mb-0">
                            <div class="avatar avatar-m rounded-circle">
                                <div class="avatar-name rounded-circle text-primary bg-primary-subtle"><span>SI</span></div>
                            </div>
                            <div class="ms-3 flex-1 bg-light p-3 rounded-3">
                                <p class="text-body-highlight fs-9 mb-0">Aguardando primeira interação manual.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast para feedback visual --}}
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080">
        <div id="syncToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="syncToastMessage">Alteração salva!</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function removeItemFromChecklist(index, btn) {
            if (!confirm('Tem certeza que deseja remover este item do checklist deste cliente?')) return;

            fetch(`/billings/operations/{{ $operation->id }}/checklist`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    item_index: index
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message);
                    btn.closest('.checklist-row-item').remove();
                }
            })
            .catch(error => console.error('Erro ao remover item:', error));
        }

        function showToast(message) {
            const toastEl = document.getElementById('syncToast');
            const toastMsg = document.getElementById('syncToastMessage');
            toastMsg.innerText = message;
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
        }
    </script>
@endpush
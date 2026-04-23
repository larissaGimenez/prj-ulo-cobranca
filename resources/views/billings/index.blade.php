@extends('layouts.app')

@section('content')
    {{-- Container Principal travado na altura da tela --}}
    <div class="kanban-page-wrapper d-flex flex-column" style="height: calc(100vh - 160px); overflow: hidden;">

        {{-- Cabeçalho --}}
        <div class="row g-3 flex-between-center mb-3 flex-shrink-0">
            <div class="col-auto">
                <nav class="mb-2" aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Esteira de Cobrança</li>
                    </ol>
                </nav>
                <h2 class="mb-0 fw-bolder">Esteira de Cobrança</h2>
            </div>
            <div class="col-auto d-flex gap-2">
                <form action="{{ route('billings.sync') }}" method="POST" id="reprocessForm">
                    @csrf
                    <button type="submit" class="btn btn-phoenix-primary px-4 shadow-none" id="btnReprocess" {{ $syncRunning ? 'disabled' : '' }}>
                        <span class="fas fa-sync me-2 {{ $syncRunning ? 'fa-spin' : '' }}" id="iconReprocess"></span>
                        <span class="spinner-border spinner-border-sm me-2 d-none" role="status" id="spinnerReprocess"></span>
                        <span id="textReprocess">{{ $syncRunning ? 'Sincronizando...' : 'Reprocessar Cards' }}</span>
                    </button>
                </form>
                <button class="btn btn-primary px-4 shadow-none" data-bs-toggle="modal" data-bs-target="#addStageModal">
                    <span class="fas fa-plus me-2"></span>Nova Etapa
                </button>
            </div>
        </div>

        @if($syncRunning)
            <div class="alert alert-subtle-primary d-flex align-items-center mb-3" role="alert">
                <span class="fas fa-info-circle me-2"></span>
                <div class="flex-1">A sincronização está sendo executada em segundo plano. Os cards serão atualizados automaticamente em alguns instantes.</div>
                <button class="btn btn-link p-0 ms-3" type="button" onclick="location.reload();">
                    <span class="fas fa-sync-alt me-1"></span>Atualizar Tela
                </button>
            </div>
        @endif

        {{-- Notificações Flutuantes --}}
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080">
            <div id="syncToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body" id="syncToastMessage">Alteração salva!</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>

        {{-- Barra de Progresso (Simulada para o trigger) --}}
        <div class="progress mb-3 d-none" id="reprocessProgress" style="height: 10px;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>

        {{-- Área do Board --}}
        <div class="d-flex overflow-x-auto pb-3 flex-1 custom-scrollbar" id="kanban-board" style="overflow-y: hidden;">
            @foreach($stages as $stage)
                <div class="kanban-column me-3 h-100 flex-shrink-0" id="column-{{ $stage->id }}"
                    style="width: 300px; transition: width 0.2s ease;">
                    <div
                        class="kanban-column-content bg-body-highlight rounded-3 border border-translucent d-flex flex-column h-100 shadow-sm">

                        {{-- Header Aberto (Estilo do segundo Kanban) --}}
                        <div class="d-flex align-items-center p-3 column-header flex-shrink-0 border-bottom border-translucent">
                            <span class="fa-solid fa-circle text-primary me-2 fs-11"></span>
                            <h5 class="mb-0 flex-1 fw-bold fs-9 text-uppercase text-nowrap">{{ $stage->name }}</h5>
                            <div class="ms-2 d-flex align-items-center">
                                <span class="badge badge-phoenix badge-phoenix-secondary fs-11 me-2">{{ $stage->operations->count() }}</span>
                                
                                <div class="dropdown">
                                    <button class="btn btn-link p-0 text-body-tertiary me-2" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="fas fa-ellipsis-v fs-11"></span>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end py-2">
                                        <a class="dropdown-item" href="#!" data-bs-toggle="modal" data-bs-target="#editStageModal-{{ $stage->id }}">Editar Etapa</a>
                                        @if($stage->operations->count() == 0)
                                            <div class="dropdown-divider"></div>
                                            <form action="{{ route('billings.destroy_stage', $stage->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta etapa?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">Excluir Etapa</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>

                                <button class="btn btn-link p-0 text-body-tertiary toggle-column"
                                    onclick="toggleColumn({{ $stage->id }})">
                                    <span class="fas fa-chevron-left fs-11"></span>
                                </button>
                            </div>
                        </div>

                        {{-- Header Colapsado --}}
                        <div class="column-collapsed-header d-none flex-column align-items-center py-3 h-100 flex-shrink-0 cursor-pointer"
                            onclick="toggleColumn({{ $stage->id }})" title="{{ $stage->name }}">
                            <span class="fa-solid fa-circle text-primary mb-3 fs-11"></span>
                            {{-- Texto removido conforme alternativa sugerida pelo usuário para evitar erro de orientação --}}
                            <span class="badge badge-phoenix badge-phoenix-secondary mt-auto fs-11">{{ $stage->operations->count() }}</span>
                        </div>

                        {{-- Itens (Cards de Cobrança - Sem Drag and Drop) --}}
                        <div class="kanban-items-container flex-1 overflow-y-auto scrollbar p-2">
                            @forelse($stage->operations as $operation)
                                @php
                                    $data = $operation->metadata;
                                    $cliente = $data['cliente'] ?? [];
                                    $empresa = $cliente['empresa'] ?? 'N/A';
                                    $totalDivida = $data['total_divida'] ?? 0;
                                    $vencidosCount = $data['vencidos_count'] ?? 0;
                                    
                                    $cardChecklist = $operation->checklist_data;
                                    if (empty($cardChecklist) && !empty($stage->checklist)) {
                                        $cardChecklist = collect($stage->checklist)->map(fn($item) => ['text' => $item, 'completed' => false, 'stage_id' => $stage->id])->toArray();
                                    }
                                    
                                    // Filtra checklist para o badge (apenas atual)
                                    $currentStageItems = collect($cardChecklist)->where('stage_id', $stage->id);
                                @endphp
                                <div class="card mb-2 shadow-none border border-translucent hover-card-style transition-base bg-white cursor-pointer" 
                                     onclick="openCardModal({{ $operation->id }}, @js($data), @js($cardChecklist ?? []), {{ $stage->id }})">
                                    <div class="card-body p-2 px-3">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <h6 class="mb-0 fw-bold text-body-highlight fs-10 text-truncate flex-1">
                                                {{ $cliente['nome'] ?? 'Cliente' }}</h6>
                                            @if($currentStageItems->isNotEmpty())
                                                @php
                                                    $completedCount = $currentStageItems->where('completed', true)->count();
                                                    $totalCount = $currentStageItems->count();
                                                    $percent = $totalCount > 0 ? ($completedCount / $totalCount) * 100 : 0;
                                                @endphp
                                                <span class="badge badge-phoenix fs-11 {{ $percent == 100 ? 'badge-phoenix-success' : 'badge-phoenix-primary' }}" title="Progresso nesta etapa">
                                                    <i class="fas fa-tasks me-1"></i>{{ $completedCount }}/{{ $totalCount }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-primary fw-bold fs-11 mb-2 text-truncate">
                                            <i class="fas fa-building me-1"></i>{{ Str::limit($empresa, 25) }}
                                        </p>
                                        <div class="bg-body-highlight rounded-2 p-1 px-2 mb-2 border border-translucent">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-body-tertiary fw-bold fs-11 uppercase">Dívida</small>
                                                <span class="text-danger fw-bolder fs-10">R$
                                                    {{ number_format($totalDivida, 2, ',', '.') }}</span>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span class="text-body-tertiary fs-11"><i
                                                    class="fas fa-file-invoice me-1"></i>{{ $vencidosCount }} títulos</span>
                                            <span class="text-primary fw-bold fs-11 uppercase">VER MAIS <i class="fas fa-eye ms-1"></i></span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5 opacity-25 empty-info">
                                    <i class="fas fa-folder-open fs-3 mb-2 d-block"></i>
                                    <p class="fs-11 mb-0 italic">Vazio</p>
                                </div>
                            @endforelse
                        </div>

                    </div>
                </div>
            @endforeach

            {{-- Botão Nova Etapa (Estilo borda tracejada do segundo kanban) --}}
            <div class="add-column-wrapper flex-shrink-0" style="min-width: 280px;">
                <button
                    class="btn btn-phoenix-secondary w-100 h-100 border-dashed border-2 d-flex flex-center flex-column py-4"
                    data-bs-toggle="modal" data-bs-target="#addStageModal">
                    <span class="fas fa-plus fs-2 mb-2"></span>
                    <span class="fw-bold fs-9 tracking-wider">ADICIONAR ETAPA</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Modal Nova Etapa --}}
    <div class="modal fade" id="addStageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border border-translucent shadow-lg text-start">
                <form action="{{ route('billings.store_stage') }}" method="POST">
                    @csrf
                    <div class="modal-header px-4 border-0">
                        <h5 class="modal-title">Nova Etapa</h5>
                        <button class="btn p-1" type="button" data-bs-dismiss="modal">
                            <span class="fas fa-times fs-9"></span>
                        </button>
                    </div>
                    <div class="modal-body px-4 pb-4 pt-0">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-body-highlight fs-10" for="stageName">Nome da Etapa</label>
                            <input class="form-control shadow-none" id="stageName" name="name" type="text" placeholder="Ex: Em Negociação" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-body-highlight fs-10 d-flex justify-content-between align-items-center">
                                Checklist Automático
                                <button type="button" class="btn btn-link p-0 fs-11" onclick="addChecklistItem('addStageChecklistContainer')">
                                    <i class="fas fa-plus-circle me-1"></i>Adicionar Item
                                </button>
                            </label>
                            <div id="addStageChecklistContainer" class="checklist-items-builder">
                                {{-- Inputs dinâmicos aqui --}}
                            </div>
                            <small class="text-body-tertiary fs-11 mt-1 d-block">Estes itens serão atribuídos automaticamente a novos cards nesta etapa.</small>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button class="btn btn-link text-body px-3 shadow-none" type="button"
                            data-bs-dismiss="modal">Cancelar</button>
                        <button class="btn btn-primary px-5 shadow-none fw-bold" type="submit">CRIAR ETAPA</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @foreach($stages as $stage)
        {{-- Modal Editar Etapa --}}
        <div class="modal fade" id="editStageModal-{{ $stage->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border border-translucent shadow-lg text-start">
                    <form action="{{ route('billings.update_stage', $stage->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header px-4 border-0">
                            <h5 class="modal-title">Editar Etapa</h5>
                            <button class="btn p-1" type="button" data-bs-dismiss="modal">
                                <span class="fas fa-times fs-9"></span>
                            </button>
                        </div>
                        <div class="modal-body px-4 pb-4 pt-0">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-body-highlight fs-10" for="stageName-{{ $stage->id }}">Nome da Etapa</label>
                                <input class="form-control shadow-none" id="stageName-{{ $stage->id }}" name="name" type="text" value="{{ $stage->name }}" required />
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-body-highlight fs-10 d-flex justify-content-between align-items-center">
                                    Checklist Automático
                                    <button type="button" class="btn btn-link p-0 fs-11" onclick="addChecklistItem('editStageChecklistContainer-{{ $stage->id }}')">
                                        <i class="fas fa-plus-circle me-1"></i>Adicionar Item
                                    </button>
                                </label>
                                <div id="editStageChecklistContainer-{{ $stage->id }}" class="checklist-items-builder">
                                    @if(is_array($stage->checklist))
                                        @foreach($stage->checklist as $item)
                                            <div class="input-group input-group-sm mb-2 checklist-row">
                                                <input type="text" name="checklist[]" class="form-control shadow-none" value="{{ $item }}">
                                                <button class="btn btn-phoenix-danger px-2 border-translucent" type="button" onclick="this.closest('.checklist-row').remove()">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <small class="text-body-tertiary fs-11 mt-1 d-block"><i class="fas fa-info-circle me-1"></i>Remover itens aqui impede novas atribuições, mas mantém nos cards que já possuem o item.</small>
                            </div>
                        </div>
                        <div class="modal-footer border-0 px-4 pb-4">
                            <button class="btn btn-link text-body px-3 shadow-none" type="button" data-bs-dismiss="modal">Cancelar</button>
                            <button class="btn btn-primary px-5 shadow-none fw-bold" type="submit">SALVAR ALTERAÇÕES</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Modal Detalhes do Card (Modal Rápido) --}}
    <div class="modal fade" id="cardModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border border-translucent shadow-lg text-start">
                <div class="modal-header px-4 border-bottom border-translucent">
                    <h5 class="modal-title" id="cardModalLabel">Detalhes da Operação</h5>
                    <button class="btn p-1" type="button" data-bs-dismiss="modal">
                        <span class="fas fa-times fs-9"></span>
                    </button>
                </div>
                <div class="modal-body px-4 py-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="avatar avatar-3xl rounded-circle border border-primary-100 me-3">
                            <div class="avatar-name rounded-circle text-primary bg-primary-subtle fs-7">
                                <span id="cardAvatar">--</span>
                            </div>
                        </div>
                        <div>
                            <h4 class="mb-1 fw-bolder text-body-highlight" id="cardClienteNome">Nome do Cliente</h4>
                            <p class="text-primary fw-bold mb-0 fs-9"><i class="fas fa-building me-1"></i><span id="cardEmpresa">Empresa</span></p>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="bg-body-highlight rounded-3 p-3 border border-translucent h-100">
                                <p class="text-body-tertiary fs-11 fw-bold text-uppercase mb-1">Dívida Total</p>
                                <h3 class="text-danger fw-bold mb-0" id="cardTotalDivida">R$ 0,00</h3>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-body-highlight rounded-3 p-3 border border-translucent h-100">
                                <p class="text-body-tertiary fs-11 fw-bold text-uppercase mb-1">Títulos Vencidos</p>
                                <h3 class="text-dark fw-bold mb-0" id="cardVencidosCount">0</h3>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="mb-3 fw-bold text-uppercase fs-10 tracking-wider"><i class="fas fa-check-double me-2 text-success"></i>Checklist de Evolução</h6>
                        <div id="cardChecklistContainer" class="list-group list-group-flush border rounded-3 overflow-hidden">
                            {{-- Gerado via JS --}}
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="#" id="cardDetailLink" class="btn btn-primary shadow-none fw-bold">
                            <i class="fas fa-external-link-alt me-2"></i>ACESSAR FICHA COMPLETA
                        </a>
                        <button class="btn btn-link text-body-tertiary shadow-none fs-9" type="button" data-bs-dismiss="modal">FECHAR</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('reprocessForm')?.addEventListener('submit', function(e) {
            const btn = document.getElementById('btnReprocess');
            const icon = document.getElementById('iconReprocess');
            const spinner = document.getElementById('spinnerReprocess');
            const text = document.getElementById('textReprocess');
            const progressContainer = document.getElementById('reprocessProgress');
            const progressBar = progressContainer.querySelector('.progress-bar');

            // UI State
            btn.classList.add('disabled');
            icon.classList.add('d-none');
            spinner.classList.remove('d-none');
            text.innerText = 'Sincronizando...';
            progressContainer.classList.remove('d-none');

            // Simulação de progresso (já que é um post síncrono que recarrega a página)
            let width = 0;
            const interval = setInterval(function() {
                if (width >= 95) {
                    clearInterval(interval);
                } else {
                    width += 5;
                    progressBar.style.width = width + '%';
                    progressBar.setAttribute('aria-valuenow', width);
                }
            }, 200);
        });

        let currentOperationId = null;

        function openCardModal(operationId, metadata, checklist, currentStageId) {
            currentOperationId = operationId;
            const cliente = metadata.cliente || {};
            
            // Preencher informações básicas
            document.getElementById('cardClienteNome').innerText = cliente.nome || 'N/A';
            document.getElementById('cardEmpresa').innerText = cliente.empresa || 'Não informada';
            document.getElementById('cardAvatar').innerText = (cliente.nome || '--').substring(0, 2).toUpperCase();
            document.getElementById('cardTotalDivida').innerText = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(metadata.total_divida || 0);
            document.getElementById('cardVencidosCount').innerText = metadata.vencidos_count || 0;
            document.getElementById('cardDetailLink').href = `/billings/${cliente.id || 0}`;

            // Preencher Checklist (Filtrado por etapa atual)
            const container = document.getElementById('cardChecklistContainer');
            container.innerHTML = '';

            if (checklist && checklist.length > 0) {
                // Filtramos e mantemos o índice original para o salvamento
                const currentItems = checklist
                    .map((item, index) => ({ ...item, originalIndex: index }))
                    .filter(item => !item.stage_id || item.stage_id == currentStageId);

                if (currentItems.length > 0) {
                    currentItems.forEach((item) => {
                        const div = document.createElement('div');
                        div.className = 'list-group-item d-flex align-items-center py-2 px-3 border-translucent checklist-row-item';
                        div.innerHTML = `
                            <div class="form-check mb-0 flex-1">
                                <input class="form-check-input cursor-pointer" type="checkbox" id="chk-${item.originalIndex}" ${item.completed ? 'checked' : ''} onchange="toggleChecklistItem(${item.originalIndex}, this.checked)">
                                <label class="form-check-label ms-2 fs-9 cursor-pointer ${item.completed ? 'text-decoration-line-through text-body-tertiary' : 'text-body-highlight'}" for="chk-${item.originalIndex}">
                                    ${item.text}
                                    ${item.is_custom ? '<span class="badge badge-phoenix badge-phoenix-info ms-1 fs-11">PERS.</span>' : ''}
                                </label>
                            </div>
                            <button class="btn btn-link p-0 text-danger-300 hover-text-danger ms-2" onclick="removeItemFromChecklist(${item.originalIndex}, this)">
                                <i class="fas fa-trash-alt fs-11"></i>
                            </button>
                        `;
                        container.appendChild(div);
                    });
                } else {
                    container.innerHTML = '<div class="list-group-item text-center py-3 text-body-tertiary fs-10 italic">Nenhuma tarefa pendente para esta etapa.</div>';
                }
            } else {
                container.innerHTML = '<div class="list-group-item text-center py-3 text-body-tertiary fs-10 italic">Nenhuma tarefa pendente para esta etapa.</div>';
            }

            const modal = new bootstrap.Modal(document.getElementById('cardModal'));
            modal.show();
        }

        function toggleChecklistItem(index, completed) {
            fetch(`/billings/operations/${currentOperationId}/checklist`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    item_index: index,
                    completed: completed
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message);
                    if (data.moved) {
                        setTimeout(() => location.reload(), 1500);
                    }
                }
            })
            .catch(error => console.error('Erro ao atualizar checklist:', error));
        }

        function showToast(message) {
            const toastEl = document.getElementById('syncToast');
            const toastMsg = document.getElementById('syncToastMessage');
            toastMsg.innerText = message;
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
        }

        function addChecklistItem(containerId) {
            const container = document.getElementById(containerId);
            const div = document.createElement('div');
            div.className = 'input-group input-group-sm mb-2 checklist-row';
            div.innerHTML = `
                <input type="text" name="checklist[]" class="form-control shadow-none" placeholder="Nova tarefa...">
                <button class="btn btn-phoenix-danger px-2 border-translucent" type="button" onclick="this.closest('.checklist-row').remove()">
                    <i class="fas fa-trash-alt"></i>
                </button>
            `;
            container.appendChild(div);
            div.querySelector('input').focus();
        }

        function removeItemFromChecklist(index, btn) {
            if (!confirm('Tem certeza que deseja remover este item do checklist deste cliente?')) return;

            fetch(`/billings/operations/${currentOperationId}/checklist`, {
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

        function toggleColumn(id) {
            const col = document.getElementById(`column-${id}`);
            const normalHeader = col.querySelector('.column-header');
            const collapsedHeader = col.querySelector('.column-collapsed-header');
            const items = col.querySelector('.kanban-items-container');

            if (col.classList.contains('collapsed')) {
                col.classList.remove('collapsed');
                col.style.width = '300px';
                normalHeader.classList.replace('d-none', 'd-flex');
                collapsedHeader.classList.replace('d-flex', 'd-none');
                items.classList.remove('d-none');
            } else {
                col.classList.add('collapsed');
                col.style.width = '64px';
                normalHeader.classList.replace('d-flex', 'd-none');
                collapsedHeader.classList.replace('d-none', 'd-flex');
                items.classList.add('d-none');
            }
        }
    </script>
@endpush

@push('css')
    <style>
        body {
            overflow: hidden !important;
        }


        .custom-scrollbar::-webkit-scrollbar {
            height: 8px;
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: var(--phoenix-gray-300);
            border-radius: 10px;
        }

        .hover-card-style:hover {
            transform: translateY(-2px);
            border-color: var(--phoenix-primary-300) !important;
            box-shadow: var(--phoenix-box-shadow-sm) !important;
        }

        .transition-base {
            transition: all 0.2s ease-in-out;
        }

        .bg-body-highlight {
            background-color: #fcfcfd !important;
        }

        .border-dashed {
            border-style: dashed !important;
        }
    </style>
@endpush
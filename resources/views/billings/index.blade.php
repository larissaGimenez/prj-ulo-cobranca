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
                                            @endphp
                                <div
                                                class="card mb-2 shadow-none border border-translucent hover-card-style transition-base bg-white">
                                                <div class="card-body p-2 px-3">
                                                    <h6 class="mb-1 fw-bold text-body-highlight fs-10 text-truncate">
                                                        {{ $cliente['nome'] ?? 'Cliente' }}</h6>
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
                                                        <a href="{{ route('billings.show', $cliente['id'] ?? 0) }}"
                                                            class="btn btn-sm btn-link p-0 text-primary fw-bold fs-11">GERENCIAR <i
                                                                class="fas fa-chevron-right ms-1"></i></a>
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
                            <label class="form-label fw-bold text-body-highlight fs-10" for="stageName">Nome da
                                Etapa</label>
                            <input class="form-control shadow-none" id="stageName" name="name" type="text"
                                placeholder="Ex: Em Negociação" required />
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
@extends('layouts.app')

@section('content')
    {{-- Container Principal travado na altura da tela --}}
    <div class="kanban-page-wrapper d-flex flex-column" style="height: calc(100vh - 180px); overflow: hidden;">

        {{-- Cabeçalho da Página --}}
        <div class="row g-3 flex-between-center mb-3 flex-shrink-0">
            <div class="col-auto">
                <h3 class="mb-1">Kanban</h3>
            </div>
        </div>

        {{-- Área do Kanban com scroll horizontal apenas --}}
        <div class="d-flex overflow-x-auto pb-2 flex-1 custom-scrollbar" id="kanban-board" style="overflow-y: hidden;">

            @php
                $columns = [
                    [
                        'id' => 'todo',
                        'title' => 'A Fazer',
                        'color' => 'primary',
                        'tasks' => [
                            ['id' => 101, 'title' => 'Cobrança Cliente #402', 'description' => 'Fatura vencida há 5 dias.', 'priority_label' => 'Urgente', 'priority_color' => 'danger', 'due_date' => 'Hoje'],
                            ['id' => 102, 'title' => 'Análise de Crédito', 'description' => 'Verificar Serasa.', 'priority_label' => 'Média', 'priority_color' => 'warning', 'due_date' => '16 Abr'],
                            ['id' => 105, 'title' => 'Liquidação Condomínio', 'description' => 'Pago via PIX.', 'priority_label' => 'Sucesso', 'priority_color' => 'success', 'due_date' => '12 Abr'],
                            ['id' => 106, 'title' => 'Acordo Metalúrgica', 'description' => 'Parcelamento em 12x.', 'priority_label' => 'Alta', 'priority_color' => 'danger', 'due_date' => 'Amanhã'],
                            ['id' => 107, 'title' => 'Cobrança Cliente #405', 'description' => 'Pendência antiga.', 'priority_label' => 'Baixa', 'priority_color' => 'info', 'due_date' => '20 Abr'],
                        ]
                    ],
                    [
                        'id' => 'doing',
                        'title' => 'Em Negociação',
                        'color' => 'warning',
                        'tasks' => [
                            ['id' => 103, 'title' => 'Acordo Metalúrgica', 'description' => 'Parcelamento em 12x.', 'priority_label' => 'Alta', 'priority_color' => 'danger', 'due_date' => 'Amanhã'],
                        ]
                    ],
                    [
                        'id' => 'done',
                        'title' => 'Concluído',
                        'color' => 'success',
                        'tasks' => [
                            ['id' => 104, 'title' => 'Liquidação Condomínio', 'description' => 'Pago via PIX.', 'priority_label' => 'Sucesso', 'priority_color' => 'success', 'due_date' => '12 Abr'],
                        ]
                    ],
                ];
            @endphp

            @foreach($columns as $column)
                <div class="kanban-column me-4 shadow-none border-0 h-100" id="column-{{ $column['id'] }}">
                    <div
                        class="kanban-column-content bg-body-emphasis rounded-3 border border-translucent d-flex flex-column h-100 shadow-sm">
                        {{-- Header da Coluna --}}
                        <div class="d-flex align-items-center p-3 column-header flex-shrink-0">
                            <span class="fa-solid fa-circle text-{{ $column['color'] }} me-2 fs-11"></span>
                            <h5 class="mb-0 flex-1 fw-bold fs-9 column-title text-nowrap">{{ $column['title'] }}</h5>
                            <div class="ms-2 d-flex align-items-center">
                                <span
                                    class="badge badge-phoenix badge-phoenix-{{ $column['color'] }} fs-11 me-2">{{ count($column['tasks']) }}</span>
                                <button class="btn btn-link p-0 text-body-tertiary toggle-column"
                                    onclick="toggleColumn('{{ $column['id'] }}')">
                                    <span class="fas fa-chevron-left fs-11"></span>
                                </button>
                            </div>
                        </div>

                        {{-- Header Colapsado --}}
                        <div class="column-collapsed-header d-none flex-column align-items-center py-3 h-100 flex-shrink-0 cursor-pointer"
                            onclick="toggleColumn('{{ $column['id'] }}')">
                            <span class="fa-solid fa-circle text-{{ $column['color'] }} mb-3 fs-11"></span>
                            <h5 class="mb-0 text-nowrap vertical-text fw-bold fs-9">{{ $column['title'] }}</h5>
                            <span
                                class="badge badge-phoenix badge-phoenix-{{ $column['color'] }} mt-auto fs-11">{{ count($column['tasks']) }}</span>
                        </div>

                        {{-- Área de Scroll Interno dos Cards --}}
                        <div class="kanban-items-container flex-1 overflow-y-auto scrollbar px-2 pb-2"
                            data-status="{{ $column['id'] }}">
                            @foreach($column['tasks'] as $task)
                                @include('kanban._card', ['task' => $task])
                            @endforeach
                        </div>

                        {{-- Footer da Coluna --}}
                        <div class="p-2 column-footer border-top border-translucent flex-shrink-0">
                            <button
                                class="btn btn-link btn-sm text-body-tertiary text-decoration-none py-1 fs-11 w-100 text-start hover-primary">
                                <span class="fas fa-plus me-1"></span>Novo item
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Adicionar Coluna --}}
            <div class="add-column-wrapper flex-shrink-0" style="min-width: 280px;">
                <button
                    class="btn btn-phoenix-secondary w-100 py-3 border-dashed border-2 bg-body-highlight-hover d-flex flex-center"
                    onclick="addNewColumn()">
                    <span class="fas fa-plus me-2"></span>Adicionar Lista
                </button>
            </div>

        </div>
    </div>

    @include('kanban.partials.add_task_modal')

    @push('scripts')
        <script>
            function toggleColumn(id) {
                const col = document.getElementById(`column-${id}`);
                const normalHeader = col.querySelector('.column-header');
                const collapsedHeader = col.querySelector('.column-collapsed-header');
                const items = col.querySelector('.kanban-items-container');
                const footer = col.querySelector('.column-footer');

                if (col.classList.contains('collapsed')) {
                    col.classList.remove('collapsed');
                    col.style.width = '300px';
                    normalHeader.classList.replace('d-none', 'd-flex');
                    collapsedHeader.classList.replace('d-flex', 'd-none');
                    items.classList.remove('d-none');
                    footer.classList.remove('d-none');
                } else {
                    col.classList.add('collapsed');
                    col.style.width = '64px';
                    normalHeader.classList.replace('d-flex', 'd-none');
                    collapsedHeader.classList.replace('d-none', 'd-flex');
                    items.classList.add('d-none');
                    footer.classList.add('d-none');
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                const containers = document.querySelectorAll('.kanban-items-container');
                containers.forEach(container => {
                    new Sortable(container, {
                        group: 'kanban',
                        animation: 250,
                        ghostClass: 'bg-primary-subtle'
                    });
                });
            });
        </script>
    @endpush

    <style>
        /* Removendo o scroll do body apenas para o Kanban */
        body {
            overflow: hidden !important;
        }

        .kanban-page-wrapper {
            /* Ajustado para caber entre navbar e footer sem transbordar */
            height: calc(100vh - 180px) !important;
        }

        .kanban-column {
            width: 300px;
            flex-shrink: 0;
            transition: width 0.2s ease;
        }

        .vertical-text {
            writing-mode: vertical-lr;
            transform: rotate(180deg);
        }

        .custom-scrollbar::-webkit-scrollbar {
            height: 6px;
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: var(--phoenix-gray-300);
            border-radius: 10px;
        }

        .bg-body-emphasis {
            background-color: #fcfcfd !important;
        }

        .kanban-items-container {
            scrollbar-width: thin;
        }
    </style>
@endsection
@extends('layouts.app')

@section('content')
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Operações</a></li>
        </ol>
    </nav>
    
        
    <div>
        <div class="mb-4">
            <h1>Novo Acordo</h1>
            <p>crie um novo acordo para o cliente.</p>
        </div>

        <form action="{{ route('negotiations.store') }}" method="POST">
            @csrf

            <div class="row mb-3">
                <div class="form-floating col-6">
                    <select name="operation_id" id="operation_id" class="form-select" aria-label="Floating label select example">
                        @foreach ($operations as $operation)
                            <option value="{{ $operation->id }}" {{ (isset($selectedOperationId) && $selectedOperationId == $operation->id) ? 'selected' : '' }}>
                                {{ $operation->cliente->nome }}
                            </option>
                        @endforeach
                    </select>
                    <label for="operation_id" class="ms-3">Operação</label>
                </div>

                <div class="form-floating col-4">
                    <select name="details[tipo_acordo]" id="tipo_acordo" class="form-select" aria-label="Floating label select example">
                        <option value="">Selecione o tipo de acordo</option>
                        <option value="À vista">À Vista</option>
                        <option value="Parcelado">Parcelado</option>
                        <option value="Prorrogação">Prorrogação</option>
                    </select>
                    <label for="tipo_acordo" class="ms-3">Tipo de Acordo</label>
                </div>
            </div>
            

            
        <div class="row mb-3">
            <div class="form-floating col-6">
                <input class="form-control text-end money-mask" name="details[valor_proposta]" id="valor_proposta" type="text" placeholder="Valor Proposta" />
                <label for="valor_proposta" class="ms-3">Valor Proposta</label>
            </div>

            <div class="form-floating col-4">
                <input class="form-control text-end money-mask" name="details[valor_entrada]" id="valor_entrada" type="text" placeholder="Valor Entrada" />
                <label for="valor_entrada" class="ms-3">Valor Entrada</label>
            </div>
        </div>


        <div class="row mb-3">
            <div class="form-floating col-6">
                <select name="status" id="status" class="form-select" aria-label="Floating label select example">
                    <option selected="">Selecione o status</option>
                    <option value="em andamento">Em Andamento</option>
                    <option value="quitado">Quitado</option>
                    <option value="cancelado">Cancelado</option>
                </select>
                <label for="status" class="ms-3">Status</label>
            </div>

            <div class="form-floating col-4">
                <input class="form-control" name="details[numero_parcelas]" id="numero_parcelas" type="number" min="1" placeholder="Número de Parcelas" />
                <label for="numero_parcelas" class="ms-3">Número de Parcelas</label>
            </div>
        </div>

        {{-- Container para as parcelas dinâmicas --}}
        <div id="parcelas_container" class="mb-4">
            {{-- Injetado via JS --}}
        </div>

        <div class="row mb-3">
            <div class="form-floating col-10">
                <textarea class="form-control" name="details[observacoes]" placeholder="Descrição da negociação" id="observacoes" style="height: 100px"></textarea>
                <label for="observacoes" class="ms-3">Observações/Detalhes</label>
            </div>
        </div>

            <div class="col-10 d-flex justify-content-end mb-3">
                <a href="{{ route('negotiations.index') }}" class="btn btn-phoenix-danger me-2">
                   Cancelar
                </a>
                <button type="submit" class="btn btn-primary">Salvar Negociação</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const numParcelasInput = document.getElementById('numero_parcelas');
    const container = document.getElementById('parcelas_container');

    function generateParcelas() {
        if (!numParcelasInput || !container) return;

        const count = parseInt(numParcelasInput.value) || 0;
        container.innerHTML = '';
        
        if (count > 0) {
            const title = document.createElement('h5');
            title.className = 'mb-3 text-body-highlight';
            title.innerHTML = '<span class="fas fa-list-ol me-2"></span>Detalhamento das Parcelas';
            container.appendChild(title);
            
            const row = document.createElement('div');
            row.className = 'row g-2';
            container.appendChild(row);

            for (let i = 1; i <= count; i++) {
                const col = document.createElement('div');
                col.className = 'col-md-3 mb-2';
                
                col.innerHTML = `
                    <div class="card border border-200 shadow-none">
                        <div class="card-body p-2">
                            <label class="form-label fs-10 text-uppercase fw-bold mb-1">Parcela ${i}</label>
                            <div class="input-group input-group-sm mb-2">
                                <span class="input-group-text">R$</span>
                                <input type="text" name="details[parcelas][${i}][valor]" class="form-control text-end money-mask" placeholder="0,00">
                            </div>
                            <input type="date" name="details[parcelas][${i}][vencimento]" class="form-control form-control-sm">
                            <input type="hidden" name="details[parcelas][${i}][id]" value="${i}">
                        </div>
                    </div>
                `;
                row.appendChild(col);
            }
        }
    }

    // Máscara de Dinheiro (pt-BR) - Começando dos centavos
    function applyMoneyMask(e) {
        let value = e.target.value.replace(/\D/g, '');
        
        // Limite de 8 dígitos (999.999,99)
        if (value.length > 8) value = value.slice(0, 8);
        
        if (value === '') {
            e.target.value = '';
            return;
        }

        // Formatação
        value = (parseFloat(value) / 100).toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        e.target.value = value;
    }

    // Delegação de evento para suportar campos dinâmicos
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('money-mask')) {
            applyMoneyMask(e);
        }
    });

    numParcelasInput.addEventListener('input', generateParcelas);
});
</script>
@endpush


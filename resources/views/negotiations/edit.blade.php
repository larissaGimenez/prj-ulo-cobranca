@extends('layouts.app')

@section('content')
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('negotiations.index') }}">Negociações</a></li>
            <li class="breadcrumb-item active">Editar</li>
        </ol>
    </nav>

    <div class="mb-4">
        <h1>Editar Negociação #{{ $negotiation->id }}</h1>
    </div>

    <form action="{{ route('negotiations.update', $negotiation) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row mb-3">
            <div class="form-floating col-6">
                <select name="operation_id" id="operation_id" class="form-select" aria-label="Floating label select example">
                    @foreach ($operations as $operation)
                        <option value="{{ $operation->id }}" {{ $negotiation->operation_id == $operation->id ? 'selected' : '' }}>
                            {{ $operation->cliente->nome }}
                        </option>
                    @endforeach
                </select>
                <label for="operation_id" class="ms-3">Operação</label>
            </div>

            <div class="form-floating col-4">
                <select name="details[tipo_acordo]" id="tipo_acordo" class="form-select" aria-label="Floating label select example">
                    <option value="">Selecione o tipo de acordo</option>
                    <option value="À vista" {{ ($negotiation->details['tipo_acordo'] ?? '') == 'À vista' ? 'selected' : '' }}>À Vista</option>
                    <option value="Parcelado" {{ ($negotiation->details['tipo_acordo'] ?? '') == 'Parcelado' ? 'selected' : '' }}>Parcelado</option>
                    <option value="Prorrogação" {{ ($negotiation->details['tipo_acordo'] ?? '') == 'Prorrogação' ? 'selected' : '' }}>Prorrogação</option>
                </select>
                <label for="tipo_acordo" class="ms-3">Tipo de Acordo</label>
            </div>
        </div>

        <div class="row mb-3">
            <div class="form-floating col-6">
                <input class="form-control text-end money-mask" name="details[valor_proposta]" id="valor_proposta" type="text" placeholder="Valor Proposta" value="{{ $negotiation->details['valor_proposta'] ?? '' }}" />
                <label for="valor_proposta" class="ms-3">Valor Proposta</label>
            </div>

            <div class="form-floating col-6">
                <input class="form-control text-end money-mask" name="details[valor_entrada]" id="valor_entrada" type="text" placeholder="Valor Entrada" value="{{ $negotiation->details['valor_entrada'] ?? '' }}" />
                <label for="valor_entrada" class="ms-3">Valor Entrada</label>
            </div>
        </div>

        <div class="row mb-3">
            <div class="form-floating col-6">
                <select name="status" id="status" class="form-select" aria-label="Floating label select example">
                    <option value="em andamento" {{ $negotiation->status == 'em andamento' ? 'selected' : '' }}>Em Andamento</option>
                    <option value="quitado" {{ $negotiation->status == 'quitado' ? 'selected' : '' }}>Quitado</option>
                    <option value="cancelado" {{ $negotiation->status == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                </select>
                <label for="status" class="ms-3">Status</label>
            </div>

            <div class="form-floating col-6">
                <input class="form-control" name="details[numero_parcelas]" id="numero_parcelas" type="number" min="1" placeholder="Número de Parcelas" value="{{ $negotiation->details['numero_parcelas'] ?? '' }}" />
                <label for="numero_parcelas" class="ms-3">Número de Parcelas</label>
            </div>
        </div>

        {{-- Container para as parcelas dinâmicas --}}
        <div id="parcelas_container" class="mb-4">
            @if(isset($negotiation->details['parcelas']))
                <h5 class="mb-3 text-body-highlight"><span class="fas fa-list-ol me-2"></span>Detalhamento das Parcelas</h5>
                <div class="row g-2">
                    @foreach($negotiation->details['parcelas'] as $parcela)
                        <div class="col-md-4 mb-2">
                            <div class="card border border-200 shadow-none">
                                <div class="card-body p-2">
                                    <label class="form-label fs-10 text-uppercase fw-bold mb-1">Parcela {{ $parcela['id'] }}</label>
                                    <div class="input-group input-group-sm mb-2">
                                        <span class="input-group-text">R$</span>
                                        <input type="text" name="details[parcelas][{{ $parcela['id'] }}][valor]" class="form-control text-end money-mask" value="{{ $parcela['valor'] ?? '' }}" placeholder="0,00">
                                    </div>
                                    <input type="date" name="details[parcelas][{{ $parcela['id'] }}][vencimento]" class="form-control form-control-sm" value="{{ $parcela['vencimento'] ?? '' }}">
                                    <input type="hidden" name="details[parcelas][{{ $parcela['id'] }}][id]" value="{{ $parcela['id'] }}">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="row mb-3">
            <div class="form-floating col-12">
                <textarea class="form-control" name="details[observacoes]" placeholder="Descrição da negociação" id="observacoes" style="height: 100px">{{ $negotiation->details['observacoes'] ?? '' }}</textarea>
                <label for="observacoes" class="ms-3">Observações/Detalhes</label>
            </div>
        </div>

        <div class="col-12 d-flex justify-content-end">
            <a href="{{ route('negotiations.index') }}" class="btn btn-phoenix-secondary me-2">
                <span class="fas fa-chevron-left me-2"></span>Voltar
            </a>
            <button type="submit" class="btn btn-primary">
                <span class="fas fa-save me-2"></span>Atualizar Negociação
            </button>
        </div>
    </form>
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
                col.className = 'col-md-4 mb-2';
                
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
        if (value.length > 8) value = value.slice(0, 8);
        if (value === '') {
            e.target.value = '';
            return;
        }
        value = (parseFloat(value) / 100).toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        e.target.value = value;
    }

    // Aplicar máscara nos campos já existentes ao carregar
    document.querySelectorAll('.money-mask').forEach(input => {
        if (input.value) {
            // Se já tem valor vindo do banco (pode estar como float), formatar
            let val = input.value.replace('.', ',');
            if (!val.includes(',')) val += ',00';
            
            // Re-aplicar máscara padrão para garantir formatação pt-BR
            let event = { target: { value: val.replace(/\D/g, '') } };
            applyMoneyMask(event);
            input.value = event.target.value;
        }
    });

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

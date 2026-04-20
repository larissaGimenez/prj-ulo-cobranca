@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Nova Negociação</h1>

        <form action="{{ route('negotiations.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="operation_id">Operação</label>
                <select name="operation_id" id="operation_id" class="form-control">
                    @foreach ($operations as $operation)
                        <option value="{{ $operation->id }}">{{ $operation->cliente->nome }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="em aberto">Em Aberto</option>
                    <option value="em andamento">Em Andamento</option>
                    <option value="concluído">Concluído</option>
                    <option value="cancelado">Cancelado</option>
                </select>
            </div>

            <div class="form-group">
                <label for="details">Detalhes</label>
                <textarea name="details" id="details" class="form-control"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>
    </div>
@endsection
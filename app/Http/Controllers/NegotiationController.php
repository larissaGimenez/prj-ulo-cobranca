<?php

namespace App\Http\Controllers;

use App\Models\Negotiation;
use App\Http\Requests\StorenegotiationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\BillingOperation;

class NegotiationController extends Controller
{
    /**
     * Lista as negociações com os dados da operação vinculada.
     */
    public function index()
    {
        $negotiations = Negotiation::with('operation.cliente')->latest()->paginate(15);
        return view('negotiations.index', compact('negotiations'));
    }

    public function create(Request $request)
    {
        $selectedOperationId = $request->query('operation_id');
        $operations = BillingOperation::with('cliente')->get();
        return view('negotiations.create', compact('operations', 'selectedOperationId'));
    }

    /**
     * Armazena uma nova negociação.
     */
    public function store(StorenegotiationRequest $request)
    {
        $negotiation = Negotiation::create($request->validated());

        return redirect()->route('negotiations.index')->with('success', 'Negociação criada com sucesso!');
    }

    /**
     * Exibe uma negociação específica.
     */
    public function show(Negotiation $negotiation)
    {
        return view('negotiations.show', compact('negotiation'));
    }

    /**
     * Exibe o formulário de edição.
     */
    public function edit(Negotiation $negotiation)
    {
        $operations = BillingOperation::with('cliente')->get();
        return view('negotiations.edit', compact('negotiation', 'operations'));
    }

    /**
     * Atualiza uma negociação existente.
     */
    public function update(StorenegotiationRequest $request, Negotiation $negotiation)
    {
        $negotiation->update($request->validated());

        return redirect()->route('negotiations.index')->with('success', 'Negociação atualizada!');
    }

    /**
     * Remove uma negociação.
     */
    public function destroy(Negotiation $negotiation)
    {
        $negotiation->delete();

        return redirect()->route('negotiations.index')->with('success', 'Negociação removida com sucesso.');
    }
}
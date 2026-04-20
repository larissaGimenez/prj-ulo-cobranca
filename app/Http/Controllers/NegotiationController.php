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
    public function index(): JsonResponse
    {
        $negotiations = Negotiation::with('operation')->latest()->paginate(15);
        return response()->json($negotiations);
    }

    public function create()
    {
        $operations = BillingOperation::with('cliente')->get();
        return view('negotiations.create', compact('operations'));
    }

    /**
     * Armazena uma nova negociação.
     */
    public function store(StorenegotiationRequest $request): JsonResponse
    {
        // O validate() já é chamado automaticamente pelo FormRequest
        $negotiation = Negotiation::create($request->validated());

        return response()->json([
            'message' => 'Negociação criada com sucesso!',
            'data' => $negotiation->load('operation')
        ], 21);
    }

    /**
     * Exibe uma negociação específica.
     */
    public function show(Negotiation $negotiation): JsonResponse
    {
        return response()->json($negotiation->load('operation'));
    }

    /**
     * Atualiza uma negociação existente (incluindo dados dentro do JSON).
     */
    public function update(Request $request, Negotiation $negotiation): JsonResponse
    {
        // Validação simples inline para o update, ou você pode criar um UpdateRequest
        $validated = $request->validate([
            'status' => 'sometimes|string',
            'details' => 'sometimes|array',
            'details.valor_proposta' => 'sometimes|numeric',
        ]);

        $negotiation->update($validated);

        return response()->json([
            'message' => 'Negociação atualizada!',
            'data' => $negotiation
        ]);
    }

    /**
     * Remove uma negociação.
     */
    public function destroy(Negotiation $negotiation): JsonResponse
    {
        $negotiation->delete();

        return response()->json([
            'message' => 'Negociação removida com sucesso.'
        ], 204);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\ClienteInadimplente;
use App\Models\TituloContaReceber;
use App\Models\BillingKanbanStage;
use App\Models\BillingOperation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BillingController extends Controller
{
    /**
     * Lista todos os clientes inadimplentes.
     */
    public function index()
    {
        // Buscamos os estágios com suas operações vinculadas (usando o snapshot metadata)
        $stages = BillingKanbanStage::with(['operations' => function($query) {
            $query->orderBy('updated_at', 'desc');
        }])->orderBy('sort_order', 'asc')->get();

        return view('billings.index', compact('stages'));
    }

    /**
     * Adiciona uma nova etapa ao Kanban.
     */
    public function storeStage(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $lastOrder = BillingKanbanStage::max('sort_order') ?? -1;

        BillingKanbanStage::create([
            'name' => $request->name,
            'sort_order' => $lastOrder + 1
        ]);

        return redirect()->back()->with('success', 'Nova etapa adicionada com sucesso!');
    }

    /**
     * Exibe os detalhes de um cliente específico e seus títulos.
     */
    public function show($id)
    {
        $cliente = ClienteInadimplente::findOrFail($id);

        // Trazemos TODOS os 17 títulos
        $titulos = TituloContaReceber::whereRaw('"cod_cliente" = ?', [(string) $cliente->cliente_id_omie])
            ->orderByRaw("to_date(data_venc, 'DD/MM/YYYY') ASC")
            ->get();

        // SOMA FINANCEIRA: Apenas o que é dívida real (Vencido e Não Pago)
        $totalDivida = $titulos->filter(function ($t) {
            $vencido = \Carbon\Carbon::createFromFormat('d/m/Y', $t->data_venc)->isPast();
            $aberto = !in_array(strtoupper($t->status), ['PAGO', 'LIQUIDADO', 'RECEBIDO']);
            return $vencido && $aberto;
        })->sum('valor_float');

        // CONTADOR DE BADGE: Apenas os atrasados
        $vencidosCount = $titulos->filter(function ($t) {
            $vencido = \Carbon\Carbon::createFromFormat('d/m/Y', $t->data_venc)->isPast();
            $aberto = !in_array(strtoupper($t->status), ['PAGO', 'LIQUIDADO', 'RECEBIDO']);
            return $vencido && $aberto;
        })->count();

        return view('billings.show', compact('cliente', 'titulos', 'vencidosCount', 'totalDivida'));
    }
}
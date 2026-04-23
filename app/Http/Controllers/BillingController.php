<?php

namespace App\Http\Controllers;

use App\Models\ClienteInadimplente;
use App\Models\TituloContaReceber;
use App\Models\BillingKanbanStage;
use App\Models\BillingOperation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class BillingController extends Controller
{
    /**
     * Sincroniza os cards do Kanban com a base de dados.
     */
    public function sync()
    {
        \App\Jobs\SyncBillingOperationsJob::dispatch();
        
        return redirect()->back()->with('success', 'Processamento iniciado em segundo plano! Os cards serão atualizados em breve.');
    }

    /**
     * Lista todos os clientes inadimplentes.
     */
    public function index()
    {
        $syncRunning = \Illuminate\Support\Facades\Cache::has('sync_billing_operations_running');

        // Buscamos os estágios com suas operações vinculadas (usando o snapshot metadata)
        $stages = BillingKanbanStage::with(['operations' => function($query) {
            $query->orderBy('updated_at', 'desc');
        }])->orderBy('sort_order', 'asc')->get();

        return view('billings.index', compact('stages', 'syncRunning'));
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
     * Atualiza o nome de uma etapa.
     */
    public function updateStage(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $stage = BillingKanbanStage::findOrFail($id);
        $stage->update(['name' => $request->name]);
        return redirect()->back()->with('success', 'Etapa atualizada!');
    }

    /**
     * Remove uma etapa se estiver vazia.
     */
    public function destroyStage($id)
    {
        $stage = BillingKanbanStage::withCount('operations')->findOrFail($id);
        if ($stage->operations_count > 0) {
            return redirect()->back()->with('error', 'Não é possível excluir uma etapa que possui cards.');
        }
        $stage->delete();
        return redirect()->back()->with('success', 'Etapa excluída!');
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

        // Trazemos a operação vinculada a este cliente (se houver) e suas negociações
        $operation = BillingOperation::with('negotiations')->where('cliente_id_omie', $cliente->cliente_id_omie)->first();

        // CÁLCULO DE DIAS EM ATRASO (Mais antigo)
        $oldestTitulo = $titulos->filter(function ($t) {
            $vencido = \Carbon\Carbon::createFromFormat('d/m/Y', $t->data_venc)->isPast();
            $aberto = !in_array(strtoupper($t->status), ['PAGO', 'LIQUIDADO', 'RECEBIDO']);
            return $vencido && $aberto;
        })->sortBy(function($t) {
            return \Carbon\Carbon::createFromFormat('d/m/Y', $t->data_venc);
        })->first();

        $diasAtraso = 0;
        if ($oldestTitulo) {
            $dataVenc = \Carbon\Carbon::createFromFormat('d/m/Y', $oldestTitulo->data_venc);
            $diasAtraso = (int) $dataVenc->diffInDays(\Carbon\Carbon::now());
        }

        return view('billings.show', compact('cliente', 'titulos', 'vencidosCount', 'totalDivida', 'operation', 'diasAtraso'));
    }
}
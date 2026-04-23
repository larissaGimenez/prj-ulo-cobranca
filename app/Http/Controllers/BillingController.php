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
            'checklist' => 'nullable|array'
        ]);

        $lastOrder = BillingKanbanStage::max('sort_order') ?? -1;

        // O checklist agora vem como array dos inputs dinâmicos
        $checklist = array_filter(array_map('trim', $request->checklist ?? []));

        BillingKanbanStage::create([
            'name' => $request->name,
            'sort_order' => $lastOrder + 1,
            'checklist' => $checklist
        ]);

        return redirect()->back()->with('success', 'Nova etapa adicionada com sucesso!');
    }

    /**
     * Atualiza o nome e o checklist de uma etapa.
     */
    public function updateStage(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'checklist' => 'nullable|array' // Mudamos para array vindo dos novos inputs
        ]);
        
        $stage = BillingKanbanStage::findOrFail($id);
        
        $oldChecklist = $stage->checklist ?? [];
        $newChecklist = array_filter(array_map('trim', $request->checklist ?? []));

        $stage->update([
            'name' => $request->name,
            'checklist' => $newChecklist
        ]);

        // Sincronizar APENAS novos itens com cards que estão nesta etapa
        $addedItems = array_diff($newChecklist, $oldChecklist);

        if (!empty($addedItems)) {
            $itemsToAppend = collect($addedItems)->map(function($item) use ($stage) {
                return ['text' => $item, 'completed' => false, 'stage_id' => $stage->id];
            })->toArray();

            $stage->operations->each(function($op) use ($itemsToAppend) {
                $currentData = $op->checklist_data ?? [];
                $op->update(['checklist_data' => array_merge($currentData, $itemsToAppend)]);
            });
        }
        
        return redirect()->back()->with('success', 'Etapa atualizada e novos itens sincronizados com os cards!');
    }

    /**
     * Adiciona um item individual ao checklist de um card.
     */
    public function addItemToChecklist(Request $request, $id)
    {
        $request->validate([
            'text' => 'required|string|max:255',
            'stage_id' => 'required|exists:billing_kanban_stages,id'
        ]);

        $operation = BillingOperation::findOrFail($id);
        $checklistData = $operation->checklist_data ?? [];

        $checklistData[] = [
            'text' => $request->text,
            'completed' => false,
            'stage_id' => (int) $request->stage_id,
            'is_custom' => true
        ];

        $operation->update(['checklist_data' => $checklistData]);

        return redirect()->back()->with('success', 'Item personalizado adicionado ao checklist!');
    }

    /**
     * Remove um item específico do checklist de um card.
     */
    public function removeItemFromChecklist(Request $request, $id)
    {
        $operation = BillingOperation::findOrFail($id);
        $checklistData = $operation->checklist_data ?? [];
        $index = $request->item_index;

        if (isset($checklistData[$index])) {
            array_splice($checklistData, $index, 1);
            $operation->update(['checklist_data' => $checklistData]);
            return response()->json(['success' => true, 'message' => 'Item removido do checklist.']);
        }

        return response()->json(['success' => false, 'message' => 'Item não encontrado.'], 404);
    }

    /**
     * Atualiza o estado do checklist de um card e avança de etapa se concluído.
     */
    public function updateChecklist(Request $request, $id)
    {
        $operation = BillingOperation::findOrFail($id);
        $itemIndex = $request->item_index;
        $completed = $request->completed;

        $checklistData = $operation->checklist_data ?? [];
        
        // Se o card não tem checklist ainda, busca do estágio atual
        if (empty($checklistData)) {
            $stage = $operation->stage;
            if ($stage && !empty($stage->checklist)) {
                $checklistData = collect($stage->checklist)->map(fn($item) => ['text' => $item, 'completed' => false, 'stage_id' => $stage->id])->toArray();
            }
        }

        if (isset($checklistData[$itemIndex])) {
            $checklistData[$itemIndex]['completed'] = $completed;
        }
        
        $operation->checklist_data = $checklistData;
        $operation->save();

        // Verificar se todos os itens deste estágio específico estão completos para avançar
        $currentStageId = $operation->billing_kanban_stage_id;
        $currentStageItems = collect($checklistData)->where('stage_id', $currentStageId);
        
        $allCurrentCompleted = $currentStageItems->isNotEmpty() && $currentStageItems->every('completed', true);

        if ($allCurrentCompleted) {
            $currentStage = $operation->stage;
            $nextStage = BillingKanbanStage::where('sort_order', '>', $currentStage->sort_order)
                ->orderBy('sort_order', 'asc')
                ->first();

            if ($nextStage) {
                // Avança para a próxima etapa
                $operation->billing_kanban_stage_id = $nextStage->id;
                
                // Soma o novo checklist ao existente
                $newItems = collect($nextStage->checklist ?? [])->map(function($item) use ($nextStage) {
                    return ['text' => $item, 'completed' => false, 'stage_id' => $nextStage->id];
                })->toArray();

                $operation->checklist_data = array_merge($checklistData, $newItems);
                $operation->save();

                return response()->json([
                    'success' => true, 
                    'moved' => true, 
                    'next_stage' => $nextStage->name,
                    'message' => 'Todas as tarefas desta etapa concluídas! Card avançou para: ' . $nextStage->name
                ]);
            }
        }

        return response()->json(['success' => true, 'moved' => false, 'message' => 'Alteração salva com sucesso!']);
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
     * Exibe os detalhes de um cliente específico e seus títulos com alta performance.
     */
    public function show($id)
    {
        $cliente = ClienteInadimplente::findOrFail($id);
        $codCliente = (string) $cliente->cliente_id_omie;

        // SQL Otimizado para cálculos financeiros (Executa tudo em uma única query rápida no Banco)
        // Nota: 'valor' é string no banco (ex: 1.250,00), precisamos converter para numeric no SQL
        $metrics = TituloContaReceber::whereRaw('"cod_cliente" = ?', [$codCliente])
            ->selectRaw("
                SUM(CASE WHEN to_date(data_venc, 'DD/MM/YYYY') < CURRENT_DATE 
                    AND UPPER(status) NOT IN ('PAGO', 'LIQUIDADO', 'RECEBIDO') 
                    THEN 
                        CASE WHEN valor LIKE '%,%' 
                             THEN CAST(REPLACE(REPLACE(valor, '.', ''), ',', '.') AS NUMERIC)
                             ELSE CAST(valor AS NUMERIC)
                        END
                    ELSE 0 END) as total_divida,
                COUNT(CASE WHEN to_date(data_venc, 'DD/MM/YYYY') < CURRENT_DATE 
                    AND UPPER(status) NOT IN ('PAGO', 'LIQUIDADO', 'RECEBIDO') 
                    THEN 1 END) as vencidos_count,
                MIN(CASE WHEN to_date(data_venc, 'DD/MM/YYYY') < CURRENT_DATE 
                    AND UPPER(status) NOT IN ('PAGO', 'LIQUIDADO', 'RECEBIDO') 
                    THEN to_date(data_venc, 'DD/MM/YYYY') END) as oldest_venc
            ")
            ->first();

        $totalDivida = $metrics->total_divida ?? 0;
        $vencidosCount = $metrics->vencidos_count ?? 0;
        $diasAtraso = $metrics->oldest_venc ? (int) Carbon::parse($metrics->oldest_venc)->diffInDays(Carbon::now()) : 0;

        // Títulos: Trazemos apenas o necessário para a listagem (limite opcional se houver milhares)
        $titulos = TituloContaReceber::whereRaw('"cod_cliente" = ?', [$codCliente])
            ->orderByRaw("to_date(data_venc, 'DD/MM/YYYY') DESC") // Mais recentes primeiro geralmente é melhor
            ->get();

        // Trazemos a operação e estágios para o modal/checklist
        $operation = BillingOperation::with('negotiations')->where('cliente_id_omie', $cliente->cliente_id_omie)->first();
        $stages = BillingKanbanStage::orderBy('sort_order', 'asc')->get();

        return view('billings.show', compact('cliente', 'titulos', 'vencidosCount', 'totalDivida', 'operation', 'diasAtraso', 'stages'));
    }
}
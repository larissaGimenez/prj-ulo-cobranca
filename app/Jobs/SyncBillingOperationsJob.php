<?php
 
namespace App\Jobs;
 
use App\Models\BillingKanbanStage;
use App\Models\BillingOperation;
use App\Models\ClienteInadimplente;
use App\Models\TituloContaReceber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
 
class SyncBillingOperationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
 
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Cache::put('sync_billing_operations_running', true, 600);
 
        try {
            // Busca a primeira etapa (Inadimplência) ou a de menor ordem
            $stageInadimplencia = BillingKanbanStage::orderBy('sort_order', 'asc')->first();
            if (!$stageInadimplencia) {
                Log::error("Sincronização abortada: Nenhuma etapa de Kanban encontrada.");
                return;
            }
 
            // Buscamos todos os clientes e seus IDs
            $clientes = ClienteInadimplente::all();
            $omieIds = $clientes->pluck('cliente_id_omie')->map(fn($id) => (string) $id)->toArray();
 
            // 1. Cálculo de métricas via SQL para máxima performance e precisão.
            // Isso delega o parsing de data e somas ao PostgreSQL, evitando problemas de memória no PHP.
            $metrics = TituloContaReceber::query()
                ->select('cod_cliente')
                ->selectRaw("
                    SUM(CASE WHEN data_venc < CURRENT_DATE 
                        AND UPPER(status) NOT IN ('PAGO', 'LIQUIDADO', 'RECEBIDO') 
                        THEN valor ELSE 0 END) as total_divida,
                    COUNT(CASE WHEN data_venc < CURRENT_DATE 
                        AND UPPER(status) NOT IN ('PAGO', 'LIQUIDADO', 'RECEBIDO') 
                        THEN 1 END) as vencidos_count,
                    MIN(CASE WHEN data_venc < CURRENT_DATE 
                        AND UPPER(status) NOT IN ('PAGO', 'LIQUIDADO', 'RECEBIDO') 
                        THEN data_venc END) as oldest_venc,
                    COUNT(*) as titulos_count
                ")
                ->whereIn('cod_cliente', $omieIds)
                ->groupBy('cod_cliente')
                ->get()
                ->keyBy('cod_cliente');
 
            // Pre-carrega operações existentes para evitar N+1 no loop
            $existingOperations = BillingOperation::whereIn('cliente_id_omie', $omieIds)
                ->get()
                ->keyBy('cliente_id_omie');
 
            $now = now();
 
            foreach ($clientes as $cliente) {
                try {
                    $omieId = (string) $cliente->cliente_id_omie;
                    $clientMetrics = $metrics->get($omieId);
 
                    // Se não houver métricas, garantimos valores zerados
                    $vencidosCount = (int) ($clientMetrics->vencidos_count ?? 0);
                    $totalDivida = (float) ($clientMetrics->total_divida ?? 0);
                    $oldestVenc = $clientMetrics->oldest_venc ?? null;
                    $titulosTotal = (int) ($clientMetrics->titulos_count ?? 0);
                    
                    $diasInadimplente = 0;
                    if ($oldestVenc) {
                        $diasInadimplente = (int) \Carbon\Carbon::parse($oldestVenc)->diffInDays($now);
                    }
 
                    $existingOp = $existingOperations->get($omieId);
                    $stageId = $existingOp ? $existingOp->billing_kanban_stage_id : $stageInadimplencia->id;
 
                    // Monta o snapshot de metadados
                    $metadata = [
                        'cliente' => $cliente->toArray(),
                        'total_divida' => $totalDivida,
                        'titulos_count' => $titulosTotal,
                        'vencidos_count' => $vencidosCount,
                        'dias_inadimplente' => $diasInadimplente
                    ];
 
                    // Recupera ou inicializa o checklist
                    $checklistData = $existingOp ? $existingOp->checklist_data : null;
                    if (empty($checklistData)) {
                        $targetStage = $existingOp ? $existingOp->stage : $stageInadimplencia;
                        if ($targetStage && !empty($targetStage->checklist)) {
                            $checklistData = collect($targetStage->checklist)->map(function($item) use ($targetStage) {
                                return ['text' => $item, 'completed' => false, 'stage_id' => $targetStage->id];
                            })->toArray();
                        }
                    }
 
                    // Atualiza ou cria a operação de cobrança
                    BillingOperation::updateOrCreate(
                        ['cliente_id_omie' => $omieId],
                        [
                            'billing_kanban_stage_id' => $stageId,
                            'metadata' => $metadata,
                            'checklist_data' => $checklistData,
                            'updated_at' => $now,
                            'data_entrada_etapa' => $existingOp ? ($existingOp->data_entrada_etapa ?? $now) : $now
                        ]
                    );
 
                } catch (\Exception $e) {
                    Log::error("Erro ao processar card para cliente Omie {$omieId}: " . $e->getMessage());
                    continue; // Pula para o próximo cliente em caso de erro individual
                }
            }
 
        } catch (\Exception $e) {
            Log::error("Erro crítico no SyncBillingOperationsJob: " . $e->getMessage());
        } finally {
            Cache::forget('sync_billing_operations_running');
        }
    }
}

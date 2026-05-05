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

class SyncBillingOperationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Cache::put('sync_billing_operations_running', true, 600);

        try {
            $stageInadimplencia = BillingKanbanStage::where('name', 'Inadimplência')->first();
            if (!$stageInadimplencia) return;

            $clientes = ClienteInadimplente::all();
            $omieIds = $clientes->pluck('cliente_id_omie')->map(fn($id) => (string) $id)->toArray();

            // Pre-fetch all necessary data in bulk
            $allTitulos = TituloContaReceber::whereIn('cod_cliente', $omieIds)->get()->groupBy('cod_cliente');
            $existingOperations = BillingOperation::whereIn('cliente_id_omie', $omieIds)
                ->with('stage')
                ->get()
                ->keyBy('cliente_id_omie');

            $upsertData = [];
            $now = now();

            foreach ($clientes as $cliente) {
                $omieId = (string) $cliente->cliente_id_omie;
                $titulos = $allTitulos->get($omieId, collect());

                $vencidosCount = 0;
                $totalDivida = 0;
                $maxDiasAtraso = 0;

                foreach ($titulos as $t) {
                    try {
                        // Otimização: Evitar Carbon se puder comparar strings ou usar lógica simples
                        // Mas para manter compatibilidade com formato Omie DD/MM/YYYY:
                        $vencDate = \Carbon\Carbon::createFromFormat('d/m/Y', $t->data_venc);
                        $aberto = !in_array(strtoupper($t->status), ['PAGO', 'LIQUIDADO', 'RECEBIDO']);
                        
                        if ($vencDate->isPast() && $aberto) {
                            $vencidosCount++;
                            $totalDivida += $t->valor_float;
                            
                            $diasAtraso = (int) $vencDate->diffInDays($now);
                            if ($diasAtraso > $maxDiasAtraso) {
                                $maxDiasAtraso = $diasAtraso;
                            }
                        }
                    } catch (\Exception $e) { continue; }
                }

                $existingOp = $existingOperations->get($omieId);
                $stageId = $existingOp ? $existingOp->billing_kanban_stage_id : $stageInadimplencia->id;

                // Metadados Snapshot
                $metadata = [
                    'cliente' => $cliente->toArray(),
                    'total_divida' => $totalDivida,
                    'titulos_count' => $titulos->count(),
                    'vencidos_count' => $vencidosCount,
                    'dias_inadimplente' => $maxDiasAtraso
                ];

                // Checklist Initialization
                $checklistData = $existingOp ? $existingOp->checklist_data : null;
                if (empty($checklistData)) {
                    $targetStage = $existingOp ? $existingOp->stage : $stageInadimplencia;
                    if ($targetStage && !empty($targetStage->checklist)) {
                        $checklistData = collect($targetStage->checklist)->map(function($item) use ($targetStage) {
                            return ['text' => $item, 'completed' => false, 'stage_id' => $targetStage->id];
                        })->toArray();
                    }
                }

                $upsertData[] = [
                    'cliente_id_omie' => $omieId,
                    'billing_kanban_stage_id' => $stageId,
                    'metadata' => $metadata,
                    'checklist_data' => $checklistData,
                    'updated_at' => $now,
                    'created_at' => $existingOp ? $existingOp->created_at : $now,
                ];
            }

            // Executa updateOrCreate individualmente para evitar erro de constraint do Postgres
            if (!empty($upsertData)) {
                foreach ($upsertData as $data) {
                    BillingOperation::updateOrCreate(
                        ['cliente_id_omie' => $data['cliente_id_omie']],
                        [
                            'billing_kanban_stage_id' => $data['billing_kanban_stage_id'],
                            'metadata' => $data['metadata'],
                            'checklist_data' => $data['checklist_data'],
                            'updated_at' => $data['updated_at'],
                            'created_at' => $data['created_at'],
                            'data_entrada_etapa' => \App\Models\BillingOperation::where('cliente_id_omie', $data['cliente_id_omie'])->value('data_entrada_etapa') ?? $now
                        ]
                    );
                }
            }

        } finally {
            Cache::forget('sync_billing_operations_running');
        }
    }
}

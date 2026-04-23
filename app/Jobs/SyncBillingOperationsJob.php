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

            if (!$stageInadimplencia) {
                return;
            }

            $clientes = ClienteInadimplente::all();
            $omieIds = $clientes->pluck('cliente_id_omie')->map(fn($id) => (string) $id)->toArray();

            // Sincronização em massa (Otimizada)
            // Pegamos todos os títulos dos clientes envolvidos de uma vez
            $allTitulos = TituloContaReceber::whereIn('cod_cliente', $omieIds)->get()->groupBy('cod_cliente');

            foreach ($clientes as $cliente) {
                $titulos = $allTitulos->get((string) $cliente->cliente_id_omie, collect());

                // SOMA FINANCEIRA: Valor real da dívida (Vencido e Aberto)
                $vencidosCount = 0;
                $totalDivida = $titulos->filter(function ($t) use (&$vencidosCount) {
                    try {
                        $vencimento = \Carbon\Carbon::createFromFormat('d/m/Y', $t->data_venc);
                        $vencido = $vencimento->isPast();
                        $aberto = !in_array(strtoupper($t->status), ['PAGO', 'LIQUIDADO', 'RECEBIDO']);
                        
                        if ($vencido && $aberto) {
                            $vencidosCount++;
                            return true;
                        }
                        return false;
                    } catch (\Exception $e) {
                        return false;
                    }
                })->sum('valor_float');

                $metadata = [
                    'cliente' => $cliente->toArray(),
                    'total_divida' => $totalDivida,
                    'titulos_count' => $titulos->count(),
                    'vencidos_count' => $vencidosCount
                ];

                $existingOperation = BillingOperation::where('cliente_id_omie', $cliente->cliente_id_omie)->first();
                $stageId = $existingOperation ? $existingOperation->billing_kanban_stage_id : $stageInadimplencia->id;

                BillingOperation::updateOrCreate(
                    ['cliente_id_omie' => $cliente->cliente_id_omie],
                    [
                        'billing_kanban_stage_id' => $stageId,
                        'metadata' => $metadata
                    ]
                );
            }
        } finally {
            Cache::forget('sync_billing_operations_running');
        }
    }
}

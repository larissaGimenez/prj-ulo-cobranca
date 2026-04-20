<?php

namespace App\Console\Commands;

use App\Models\BillingKanbanStage;
use App\Models\BillingOperation;
use App\Models\ClienteInadimplente;
use App\Models\TituloContaReceber;
use Illuminate\Console\Command;

class SyncBillingOperations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-billing-operations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza clientes inadimplentes para a mesa de operações (Kanban)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando sincronização de operações...');

        $stageInadimplencia = BillingKanbanStage::where('name', 'Inadimplência')->first();

        if (!$stageInadimplencia) {
            $this->error('Estágio "Inadimplência" não encontrado. Execute o seeder primeiro.');
            return 1;
        }

        $clientes = ClienteInadimplente::all();
        $bar = $this->output->createProgressBar(count($clientes));

        $bar->start();

        foreach ($clientes as $cliente) {
            // Busca títulos do cliente
            $titulos = TituloContaReceber::whereRaw('"cod_cliente"::bigint = ?', [$cliente->cliente_id_omie])->get();

            // SOMA FINANCEIRA: Valor real da dívida (Vencido e Aberto)
            $totalDivida = $titulos->filter(function ($t) {
                try {
                    $vencido = \Carbon\Carbon::createFromFormat('d/m/Y', $t->data_venc)->isPast();
                    $aberto = !in_array(strtoupper($t->status), ['PAGO', 'LIQUIDADO', 'RECEBIDO']);
                    return $vencido && $aberto;
                } catch (\Exception $e) {
                    return false;
                }
            })->sum('valor_float');

            $metadata = [
                'cliente' => $cliente->toArray(),
                'titulos' => $titulos->toArray(),
                'total_divida' => $totalDivida,
                'titulos_count' => $titulos->count(),
                'vencidos_count' => $titulos->filter(function($t) {
                     try {
                        $vencido = \Carbon\Carbon::createFromFormat('d/m/Y', $t->data_venc)->isPast();
                        $aberto = !in_array(strtoupper($t->status), ['PAGO', 'LIQUIDADO', 'RECEBIDO']);
                        return $vencido && $aberto;
                    } catch (\Exception $e) { return false; }
                })->count()
            ];

            BillingOperation::updateOrCreate(
                ['cliente_id_omie' => $cliente->cliente_id_omie],
                [
                    'billing_kanban_stage_id' => $stageInadimplencia->id,
                    'metadata' => $metadata
                ]
            );

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Sincronização concluída com sucesso!');

        return 0;
    }
}

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
        $this->info('Iniciando sincronização de operações (via Job)...');
        \App\Jobs\SyncBillingOperationsJob::dispatchSync();
        $this->info('Sincronização concluída com sucesso!');
        return 0;
    }
}

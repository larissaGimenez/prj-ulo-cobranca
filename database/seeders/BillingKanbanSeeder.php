<?php

namespace Database\Seeders;

use App\Models\BillingKanbanStage;
use Illuminate\Database\Seeder;

class BillingKanbanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stages = [
            'Inadimplência',
            'Contato Inicial',
            'Em Negociação',
            'Proposta Enviada',
            'Acordo Fechado',
            'Pagamento Concluído'
        ];

        foreach ($stages as $index => $name) {
            BillingKanbanStage::updateOrCreate(
                ['name' => $name],
                ['sort_order' => $index]
            );
        }
    }
}

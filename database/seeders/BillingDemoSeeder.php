<?php

namespace Database\Seeders;

use App\Models\BillingKanbanStage;
use App\Models\BillingOperation;
use App\Models\ClienteInadimplente;
use App\Models\TituloContaReceber;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BillingDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stages = BillingKanbanStage::all();
        if ($stages->isEmpty()) {
            $this->call(BillingKanbanSeeder::class);
            $stages = BillingKanbanStage::all();
        }

        $clientes = [
            [
                'id' => 1001,
                'cliente_id_omie' => '123456789',
                'nome' => 'ACME Corporation Ltda',
                'cpf_cnpj' => '12.345.678/0001-90',
                'empresa' => 1,
                'contato' => 'Sr. Wile E. Coyote'
            ],
            [
                'id' => 1002,
                'cliente_id_omie' => '987654321',
                'nome' => 'Stark Industries',
                'cpf_cnpj' => '98.765.432/0001-10',
                'empresa' => 1,
                'contato' => 'Tony Stark'
            ],
            [
                'id' => 1003,
                'cliente_id_omie' => '555444333',
                'nome' => 'Wayne Enterprises',
                'cpf_cnpj' => '55.544.433/0001-22',
                'empresa' => 1,
                'contato' => 'Bruce Wayne'
            ],
            [
                'id' => 1004,
                'cliente_id_omie' => '111222333',
                'nome' => 'Daily Planet',
                'cpf_cnpj' => '11.122.233/0001-44',
                'empresa' => 1,
                'contato' => 'Lois Lane'
            ],
            [
                'id' => 1005,
                'cliente_id_omie' => '999888777',
                'nome' => 'Umbrella Corp',
                'cpf_cnpj' => '99.988.877/0001-55',
                'empresa' => 1,
                'contato' => 'Albert Wesker'
            ]
        ];

        foreach ($clientes as $cData) {
            // Criar ou atualizar o Cliente
            $cliente = ClienteInadimplente::updateOrCreate(
                ['cliente_id_omie' => $cData['cliente_id_omie']],
                [
                    'id' => $cData['id'],
                    'nome' => $cData['nome'],
                    'cpf_cnpj' => $cData['cpf_cnpj'],
                    'contato' => $cData['contato'],
                    'empresa' => $cData['empresa'],
                    'data_criacao' => now(),
                    'cliente' => ['segmento' => 'Teste', 'cidade' => 'São Paulo']
                ]
            );

            // Criar Títulos para o cliente
            $numTitulos = rand(2, 5);
            $totalDivida = 0;
            $vencidosCount = 0;

            for ($i = 1; $i <= $numTitulos; $i++) {
                $valor = rand(500, 5000) + (rand(0, 99) / 100);
                $diasAtraso = rand(10, 90);
                $vencimento = Carbon::now()->subDays($diasAtraso);
                
                TituloContaReceber::updateOrCreate(
                    ['id' => ($cliente->id * 100) + $i],
                    [
                        'cod_lanc' => ($cliente->id * 1000) + $i,
                        'data_venc' => $vencimento->format('d/m/Y'),
                        'numero_parcela' => $i . '/' . $numTitulos,
                        'empresa' => $cliente->empresa,
                        'status' => 'Aberto',
                        'valor' => number_format($valor, 2, ',', '.'),
                        'cod_cliente' => $cliente->cliente_id_omie,
                        'data_criacao' => now(),
                        'dados_titulo' => ['projeto' => 'Serviços Demo']
                    ]
                );

                $totalDivida += $valor;
                $vencidosCount++;
            }

            // Criar Operação no Kanban (Distribuir entre os estágios)
            $stage = $stages->random();
            BillingOperation::updateOrCreate(
                ['cliente_id_omie' => $cliente->cliente_id_omie],
                [
                    'billing_kanban_stage_id' => $stage->id,
                    'metadata' => [
                        'cliente' => [
                            'id' => $cliente->id,
                            'nome' => $cliente->nome,
                            'empresa' => $cliente->empresa,
                        ],
                        'total_divida' => $totalDivida,
                        'vencidos_count' => $vencidosCount,
                    ]
                ]
            );
        }
    }
}

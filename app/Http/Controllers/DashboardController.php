<?php

namespace App\Http\Controllers;

use App\Models\BillingOperation;
use App\Models\Negotiation;
use App\Models\User;
use App\Models\BillingKanbanStage;
use App\Models\ClienteInadimplente;
use App\Models\TituloContaReceber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $lastWeek = Carbon::now()->subWeek();

        // 1. Estatísticas Atuais
        $totalDebtors = ClienteInadimplente::count();
        $totalDebtValue = BillingOperation::query()
            ->select(DB::raw("SUM(CAST(metadata->>'total_divida' AS NUMERIC)) as total"))
            ->value('total') ?? 0;
        $activeAgreementsCount = Negotiation::where('status', 'em andamento')->count();
        $finishedAgreementsCount = Negotiation::where('status', 'quitado')->count();
        $totalAgreements = $activeAgreementsCount + $finishedAgreementsCount;

        // 2. Estatísticas da Semana Passada (Diferença)
        $debtorsLastWeek = ClienteInadimplente::where('data_criacao', '<', $lastWeek)->count();
        $agreementsLastWeek = Negotiation::where('created_at', '<', $lastWeek)->count();
        
        // Para dívida, estimamos baseado nos títulos criados na última semana
        $newDebtLastWeek = TituloContaReceber::where('data_criacao', '>=', $lastWeek)
            ->get()
            ->sum(fn($t) => (float) str_replace(['.', ','], ['', '.'], $t->valor));

        $stats = [
            'total_debtors' => $totalDebtors,
            'debtors_diff' => $totalDebtors - $debtorsLastWeek,
            
            'total_debt_value' => $totalDebtValue,
            'debt_diff' => $newDebtLastWeek, // Incremento real de novos títulos
            
            'total_agreements' => $totalAgreements,
            'agreements_diff' => $totalAgreements - $agreementsLastWeek,

            'active_agreements' => $activeAgreementsCount,
            'total_negotiated' => Negotiation::query()
                ->select(DB::raw("SUM(CAST(details->>'valor_proposta' AS NUMERIC)) as total"))
                ->value('total') ?? 0,
        ];

        // Ticket Médio
        $stats['average_ticket'] = $stats['total_negotiated'] > 0 && $totalAgreements > 0 
            ? $stats['total_negotiated'] / $totalAgreements 
            : 0;

        // Negociações Criadas por Dia (Últimos 15 dias)
        $negotiationsPerDay = Negotiation::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(15))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        $chartDaily = [
            'dates' => $negotiationsPerDay->pluck('date')->map(fn($d) => Carbon::parse($d)->format('d/m')),
            'values' => $negotiationsPerDay->pluck('count')
        ];

        // Projeção de Recebimento (30 dias)
        $projection = 0;
        $next30Days = Carbon::now()->addDays(30);
        $activeNegotiations = Negotiation::where('status', 'em andamento')->get();
        foreach ($activeNegotiations as $neg) {
            $parcelas = $neg->details['parcelas'] ?? [];
            foreach ($parcelas as $p) {
                $venc = Carbon::parse($p['vencimento'] ?? null);
                if ($venc->isBetween(Carbon::now(), $next30Days)) {
                    $projection += (float) str_replace(['.', ','], ['', '.'], $p['valor'] ?? 0);
                }
            }
        }
        $stats['projection_30d'] = $projection;

        // Dívida por Empresa
        $debtsByCompany = BillingOperation::query()
            ->select(
                DB::raw("COALESCE(metadata->'cliente'->>'empresa', 'Não Informada') as company_name"),
                DB::raw("count(*) as debtors_count"),
                DB::raw("SUM(CAST(metadata->>'total_divida' AS NUMERIC)) as total_debt")
            )
            ->groupBy(DB::raw("COALESCE(metadata->'cliente'->>'empresa', 'Não Informada')"))
            ->orderByDesc('total_debt')
            ->limit(10)
            ->get();

        // Dados para Gráfico de Rosca (Status)
        $chartStatus = [
            ['name' => 'Em Andamento', 'value' => $activeAgreementsCount],
            ['name' => 'Quitados', 'value' => $finishedAgreementsCount],
            ['name' => 'Cancelados', 'value' => Negotiation::where('status', 'cancelado')->count()],
        ];

        // Dados para Gráfico de Etapas
        $stages = BillingKanbanStage::withCount('operations')->orderBy('sort_order')->get();
        $chartStages = $stages->map(fn($s) => [
            'name' => $s->name,
            'value' => $s->operations_count,
            'percentage' => $totalDebtors > 0 ? round(($s->operations_count / $totalDebtors) * 100) : 0
        ]);

        return view('dashboard', compact('stats', 'chartStatus', 'chartStages', 'debtsByCompany', 'chartDaily'));
    }
}

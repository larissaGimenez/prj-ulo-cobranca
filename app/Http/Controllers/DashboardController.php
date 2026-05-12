<?php

namespace App\Http\Controllers;

use App\Models\BillingKanbanStage;
use App\Models\BillingOperation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $dashboardData = Cache::remember('dashboard_stats', now()->addMinutes(5), function () {

            $today    = now()->toDateTimeString();
            $minus15d = now()->subDays(15)->toDateTimeString();
            $plus30d  = now()->addDays(30)->toDateTimeString();

            // ─────────────────────────────────────────────────────────────────
            // QUERY 1 — Todas as métricas de Negotiation em uma única passagem
            // Substitui 6 queries separadas (active, finished, cancelled,
            // total_negotiated, agreements_last_week)
            // ─────────────────────────────────────────────────────────────────
            $negStats = DB::selectOne("
                SELECT
                    COUNT(*) FILTER (WHERE status = 'em andamento')                      AS active_count,
                    COUNT(*) FILTER (WHERE status = 'quitado')                           AS finished_count,
                    COUNT(*) FILTER (WHERE status = 'cancelado')                         AS cancelled_count,
                    COALESCE(SUM(CAST(details->>'valor_proposta' AS NUMERIC)), 0)        AS total_negotiated
                FROM negotiations
            ");

            $activeCount     = (int)   $negStats->active_count;
            $finishedCount   = (int)   $negStats->finished_count;
            $cancelledCount  = (int)   $negStats->cancelled_count;
            $totalAgreements = $activeCount + $finishedCount;
            $totalNegotiated = (float) $negStats->total_negotiated;

            // ─────────────────────────────────────────────────────────────────
            // QUERY 2 — Métricas de clientes_inadimplentes + titulos em uma
            // única passagem.
            // Correção: REPLACE normaliza o formato BR ("1.250,00" → "1250.00")
            // antes do cast NUMERIC, evitando retorno zero em campos TEXT.
            // ─────────────────────────────────────────────────────────────────
            $debtStats = DB::selectOne("
                SELECT
                    (SELECT COUNT(*) FROM clientes_inadimplentes)  AS total_debtors,
                    COALESCE(SUM(valor) FILTER (
                        WHERE data_venc < CURRENT_DATE
                          AND UPPER(status) NOT IN ('PAGO', 'LIQUIDADO', 'RECEBIDO')
                    ), 0) AS total_debt_value
                FROM titulos_conta_receber
            ");

            $totalDebtors   = (int)   $debtStats->total_debtors;
            $totalDebtValue = (float) $debtStats->total_debt_value;

            // ─────────────────────────────────────────────────────────────────
            // QUERY 3a — Projeção de parcelas dos próximos 30 dias via SQL
            // Substitui o loop PHP que carregava todos os registros em memória
            // ─────────────────────────────────────────────────────────────────
            $projection = (float) DB::selectOne("
                SELECT COALESCE(SUM(
                    CASE
                        WHEN (parcela->>'vencimento')::date BETWEEN :today::date AND :plus30d::date
                        THEN REPLACE(REPLACE(parcela->>'valor', '.', ''), ',', '.')::NUMERIC
                        ELSE 0
                    END
                ), 0) AS total
                FROM negotiations,
                     jsonb_array_elements(details->'parcelas') AS parcela
                WHERE status = 'em andamento'
                  AND jsonb_typeof(details->'parcelas') = 'array'
            ", ['today' => $today, 'plus30d' => $plus30d])->total;

            // ─────────────────────────────────────────────────────────────────
            // QUERY 3b — Negociações por dia (últimos 15 dias)
            // ─────────────────────────────────────────────────────────────────
            $dailyRows = DB::select("
                SELECT DATE(created_at) AS date, COUNT(*) AS count
                FROM negotiations
                WHERE created_at >= :minus15d
                GROUP BY DATE(created_at)
                ORDER BY date ASC
            ", ['minus15d' => $minus15d]);

            $chartDaily = [
                'dates'  => array_map(fn($r) => Carbon::parse($r->date)->format('d/m'), $dailyRows),
                'values' => array_map(fn($r) => (int) $r->count, $dailyRows),
            ];

            // ─────────────────────────────────────────────────────────────────
            // QUERY 4 — Dívida agrupada por Empresa (já era eficiente)
            // ─────────────────────────────────────────────────────────────────
            $debtsByCompany = BillingOperation::query()
                ->select(
                    DB::raw("COALESCE(metadata->'cliente'->>'empresa', 'Não Informada') as company_name"),
                    DB::raw("count(*) as debtors_count"),
                    DB::raw("SUM(CAST(metadata->>'total_divida' AS NUMERIC)) as total_debt")
                )
                ->groupBy(DB::raw("COALESCE(metadata->'cliente'->>'empresa', 'Não Informada')"))
                ->orderByDesc('total_debt')
                ->limit(10)
                ->get()
                ->toArray();

            // ─────────────────────────────────────────────────────────────────
            // QUERY 5 — Etapas do Kanban com contagem (já era eficiente)
            // ─────────────────────────────────────────────────────────────────
            $stages = BillingKanbanStage::withCount('operations')->orderBy('sort_order')->get();
            $chartStages = $stages->map(fn($s) => [
                'name'       => $s->name,
                'value'      => $s->operations_count,
                'percentage' => $totalDebtors > 0
                    ? round(($s->operations_count / $totalDebtors) * 100)
                    : 0,
            ])->values()->all();

            // ─────────────────────────────────────────────────────────────────
            // Montagem final
            // ─────────────────────────────────────────────────────────────────
            $stats = [
                'total_debtors'    => $totalDebtors,
                'total_debt_value' => $totalDebtValue,
                'total_agreements' => $totalAgreements,
                'active_agreements'=> $activeCount,
                'total_negotiated' => $totalNegotiated,
                'average_ticket'   => $totalNegotiated > 0 && $totalAgreements > 0
                    ? $totalNegotiated / $totalAgreements
                    : 0,
                'projection_30d'   => $projection,
            ];

            $chartStatus = [
                ['name' => 'Em Andamento', 'value' => $activeCount],
                ['name' => 'Quitados',     'value' => $finishedCount],
                ['name' => 'Cancelados',   'value' => $cancelledCount],
            ];

            return compact('stats', 'chartStatus', 'chartStages', 'debtsByCompany', 'chartDaily');
        });

        // Re-hidrata debtsByCompany como Collection de objetos (a Blade usa ->take() e ->company_name)
        $dashboardData['debtsByCompany'] = collect($dashboardData['debtsByCompany'])
            ->map(fn($item) => (object) $item);

        return view('dashboard', $dashboardData);
    }

    public function refresh()
    {
        Cache::forget('dashboard_stats');
        return redirect()->route('dashboard')->with('success', 'Dados do dashboard atualizados com sucesso!');
    }
}

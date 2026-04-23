@extends('layouts.app')

@section('content')
<div class="pb-5">
    <div class="row g-4">
        {{-- Título --}}
        <div class="col-12">
            <h2 class="mb-2 text-body-emphasis">Dashboard de Cobrança</h2>
            <h5 class="text-body-tertiary fw-semibold">Acompanhamento de performance e recuperação de crédito em um só lugar.</h5>
        </div>

        {{-- Primeira Seção: Cards Principais e Ranking por Empresa --}}
        <div class="col-12 col-xxl-9">
            <div class="row g-3">
                {{-- Card 1: Clientes Inadimplentes --}}
                <div class="col-12 col-md-4">
                    <div class="card h-100 border border-translucent shadow-none">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar avatar-s me-2">
                                    <div class="avatar-name rounded bg-primary-subtle text-primary d-flex flex-center">
                                        <span class="fas fa-users fs-10"></span>
                                    </div>
                                </div>
                                <h6 class="mb-0 text-body-tertiary fw-bold">CLIENTES DEVEDORES</h6>
                            </div>
                            <h2 class="text-primary mb-3">{{ number_format($stats['total_debtors'], 0, ',', '.') }}</h2>
                            <div class="d-flex align-items-center">
                                <span class="badge badge-phoenix badge-phoenix-success fs-10 me-2">+{{ $stats['debtors_diff'] }}</span>
                                <span class="text-body-tertiary fs-10 fw-semibold text-nowrap">vs semana passada</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Total da Dívida --}}
                <div class="col-12 col-md-4">
                    <div class="card h-100 border border-translucent shadow-none">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar avatar-s me-2">
                                    <div class="avatar-name rounded bg-danger-subtle text-danger d-flex flex-center">
                                        <span class="fas fa-dollar-sign fs-10"></span>
                                    </div>
                                </div>
                                <h6 class="mb-0 text-body-tertiary fw-bold">TOTAL DA DÍVIDA</h6>
                            </div>
                            <h2 class="text-danger mb-3">R$ {{ number_format($stats['total_debt_value'], 0, ',', '.') }}</h2>
                            <div class="d-flex align-items-center">
                                <span class="badge badge-phoenix badge-phoenix-danger fs-10 me-2">+ R$ {{ number_format($stats['debt_diff'], 0, ',', '.') }}</span>
                                <span class="text-body-tertiary fs-10 fw-semibold text-nowrap">novos títulos</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 3: Total de Acordos --}}
                <div class="col-12 col-md-4">
                    <div class="card h-100 border border-translucent shadow-none">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar avatar-s me-2">
                                    <div class="avatar-name rounded bg-success-subtle text-success d-flex flex-center">
                                        <span class="fas fa-handshake fs-10"></span>
                                    </div>
                                </div>
                                <h6 class="mb-0 text-body-tertiary fw-bold">TOTAL DE ACORDOS</h6>
                            </div>
                            <h2 class="text-success mb-3">{{ number_format($stats['total_agreements'], 0, ',', '.') }}</h2>
                            <div class="d-flex align-items-center">
                                <span class="badge badge-phoenix badge-phoenix-success fs-10 me-2">+{{ $stats['agreements_diff'] }}</span>
                                <span class="text-body-tertiary fs-10 fw-semibold text-nowrap">vs semana passada</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ranking de Empresas (Lado Direito) --}}
        <div class="col-12 col-xxl-3">
            <div class="card border border-translucent shadow-none h-100">
                <div class="card-header py-3 ps-4 border-bottom border-translucent">
                    <h6 class="mb-0 text-body-emphasis fw-bold">DEVEDORES POR EMPRESA</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush scrollbar" style="max-height: 250px;">
                        @foreach($debtsByCompany->take(5) as $company)
                        <div class="list-group-item bg-transparent border-translucent py-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="fw-bold fs-10 text-body-emphasis">{{ $loop->iteration }}. {{ Str::limit($company->company_name, 20) }}</span>
                            </div>
                            <span class="badge badge-phoenix badge-phoenix-secondary fs-10 text-body-emphasis fw-bold">({{ $company->debtors_count }})</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Segunda Seção: Tendência e Projeções --}}
        <div class="col-12 col-xl-8">
            <div class="card border border-translucent shadow-none">
                <div class="card-header py-3 ps-4 border-bottom border-translucent">
                    <h5 class="mb-0 text-body-emphasis">Crescimento de Acordos</h5>
                    <p class="mb-0 fs-10 text-body-tertiary">Performance diária de fechamento</p>
                </div>
                <div class="card-body">
                    <div id="chart-daily-negotiations" style="min-height: 300px; width: 100%;"></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="row g-3">
                <div class="col-12">
                    <div class="card border border-translucent shadow-none bg-body-highlight">
                        <div class="card-body">
                            <h6 class="text-body-tertiary mb-1 fs-10 fw-bold">PROJEÇÃO DE RECEBIMENTO</h6>
                            <h3 class="text-success">R$ {{ number_format($stats['projection_30d'], 0, ',', '.') }}</h3>
                            <p class="mb-0 fs-11 text-body-tertiary mt-2">Próximos 30 dias de parcelas</p>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card border border-translucent shadow-none bg-body-highlight">
                        <div class="card-body">
                            <h6 class="text-body-tertiary mb-1 fs-10 fw-bold">TICKET MÉDIO</h6>
                            <h3 class="text-info">R$ {{ number_format($stats['average_ticket'], 0, ',', '.') }}</h3>
                            <p class="mb-0 fs-11 text-body-tertiary mt-2">Valor médio por acordo</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Terceira Seção: Status e Etapas --}}
        <div class="col-12 col-lg-7">
            <div class="card h-100 border border-translucent shadow-none">
                <div class="card-header py-3 ps-4 border-bottom border-translucent">
                    <h5 class="mb-0 text-body-emphasis">Status das Negociações</h5>
                </div>
                <div class="card-body pt-4">
                    <div class="row g-4 align-items-center">
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                @foreach($chartStatus as $item)
                                <li class="list-group-item bg-transparent d-flex justify-content-between px-0 border-translucent py-3">
                                    <div class="d-flex align-items-center">
                                        <span class="fa-solid fa-circle fs-11 me-2" style="color: {{ $loop->index == 0 ? '#3874ff' : ($loop->index == 1 ? '#25b003' : '#ff3b3b') }}"></span>
                                        <span class="fw-bold fs-9 text-body-emphasis">{{ $item['name'] }}</span>
                                    </div>
                                    <span class="fw-bold fs-9 text-body-emphasis">{{ $item['value'] }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-md-6 text-center">
                            <div id="chart-status-donut" style="min-height: 280px; width: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="card h-100 border border-translucent shadow-none">
                <div class="card-header py-3 ps-4 border-bottom border-translucent">
                    <h5 class="mb-0 text-body-emphasis">Conversão por Etapa</h5>
                </div>
                <div class="card-body pt-3">
                    @foreach($chartStages as $stage)
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <h6 class="mb-0 text-body-emphasis fs-10 text-uppercase fw-bold">{{ $stage['name'] }}</h6>
                            <span class="fs-10 fw-bold text-primary">{{ $stage['percentage'] }}%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar rounded" role="progressbar" 
                                 style="width: {{ $stage['percentage'] }}%; background-color: #3874ff;" 
                                 aria-valuenow="{{ $stage['percentage'] }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const theme = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light';
        
        // 1. Donut Chart
        const chartStatusDom = document.getElementById('chart-status-donut');
        const chartStatus = echarts.init(chartStatusDom, theme);
        const totalNegotiations = {{ array_sum(array_column($chartStatus, 'value')) }};
        
        const optionStatus = {
            tooltip: { trigger: 'item', padding: [7, 10] },
            color: ['#3874ff', '#25b003', '#ff3b3b'],
            series: [{
                name: 'Status',
                type: 'pie',
                radius: ['65%', '85%'],
                avoidLabelOverlap: false,
                itemStyle: { borderRadius: 4, borderColor: 'transparent', borderWidth: 2 },
                label: {
                    show: true,
                    position: 'center',
                    formatter: '{total|' + totalNegotiations + '}\n{sub|Total}',
                    rich: {
                        total: { fontSize: 32, fontWeight: 'bold', color: theme === 'dark' ? '#fff' : '#000' },
                        sub: { fontSize: 14, color: '#9fa6bc', padding: [5, 0, 0, 0] }
                    }
                },
                data: @json($chartStatus)
            }]
        };
        chartStatus.setOption(optionStatus);

        // 2. Crescimento Diário (Line Chart)
        const chartDailyDom = document.getElementById('chart-daily-negotiations');
        const chartDaily = echarts.init(chartDailyDom, theme);
        const dailyDates = @json($chartDaily['dates']);
        const dailyValues = @json($chartDaily['values']);

        const optionDaily = {
            tooltip: { trigger: 'axis', axisPointer: { type: 'line' } },
            grid: { left: '3%', right: '4%', bottom: '3%', top: '5%', containLabel: true },
            xAxis: {
                type: 'category',
                boundaryGap: false,
                data: dailyDates,
                axisLine: { show: false },
                axisTick: { show: false }
            },
            yAxis: {
                type: 'value',
                splitLine: { lineStyle: { type: 'dashed', color: '#eff2f6' } },
                axisLine: { show: false }
            },
            series: [{
                name: 'Novos Acordos',
                type: 'line',
                smooth: true,
                symbol: 'circle',
                symbolSize: 8,
                itemStyle: { color: '#3874ff' },
                lineStyle: { width: 3 },
                areaStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: 'rgba(56, 116, 255, 0.2)' },
                        { offset: 1, color: 'rgba(56, 116, 255, 0)' }
                    ])
                },
                data: dailyValues
            }]
        };
        chartDaily.setOption(optionDaily);

        window.addEventListener('resize', () => {
            chartStatus.resize();
            chartDaily.resize();
        });
    });
</script>
@endpush

<style>
    .card { border-radius: 0.75rem; }
</style>
@endsection
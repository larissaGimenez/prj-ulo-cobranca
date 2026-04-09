@extends('layouts.app')

@section('content')
    <div class="pb-5">
        <div class="row g-4">
            <div class="col-12">
                <h2 class="mb-2 text-body-emphasis">Menu Principal</h2>
                <h5 class="text-body-tertiary fw-semibold">Selecione o app que deseja acessar</h5>
            </div>

            {{-- CADASTROS --}}
            @can('access.users')
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                    <a href="{{ route('users.index') }}" class="text-decoration-none">
                        <div class="card h-100 card-flyer hover-actions-trigger shadow-sm border-translucent">
                            <div class="card-body d-flex align-items-center">
                                <div class="avatar avatar-xl me-3 flex-shrink-0">
                                    <div class="avatar-name rounded bg-primary-subtle text-primary d-flex flex-center">
                                        <span class="fas fa-user-edit fs-6"></span>
                                    </div>
                                </div>
                                <div>
                                    <h5 class="text-body-emphasis mb-1">CADASTROS</h5>
                                    <p class="text-body-tertiary fs-10 mb-0">Controle de cadastro de usuários, clientes e
                                        perfil.</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endcan

            {{-- COBRANÇA --}}
            @can('access.billings')
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                    <a href="{{ route('billings.index') }}" class="text-decoration-none">
                        <div class="card h-100 card-flyer hover-actions-trigger shadow-sm border-translucent">
                            <div class="card-body d-flex align-items-center">
                                <div class="avatar avatar-xl me-3 flex-shrink-0">
                                    <div class="avatar-name rounded bg-warning-subtle text-warning d-flex flex-center">
                                        <span class="fas fa-dollar-sign fs-6"></span>
                                    </div>
                                </div>
                                <div>
                                    <h5 class="text-body-emphasis mb-1">COBRANÇA</h5>
                                    <p class="text-body-tertiary fs-10 mb-0">Controle de cobrança, indicadores de vendas e
                                        cupons.</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endcan

            {{-- COMERCIAL --}}
            @can('access.sales')
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                    <a href="{{ route('sales.index') }}" class="text-decoration-none">
                        <div class="card h-100 card-flyer hover-actions-trigger shadow-sm border-translucent">
                            <div class="card-body d-flex align-items-center">
                                <div class="avatar avatar-xl me-3 flex-shrink-0">
                                    <div class="avatar-name rounded bg-success-subtle text-success d-flex flex-center">
                                        <span class="fas fa-chart-line fs-6"></span>
                                    </div>
                                </div>
                                <div>
                                    <h5 class="text-body-emphasis mb-1">COMERCIAL</h5>
                                    <p class="text-body-tertiary fs-10 mb-0">Controle de pedidos, indicadores de vendas e
                                        cupons.</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endcan

            {{-- FINANCEIRO --}}
            @can('access.finances')
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                    <a href="{{ route('finances.index') }}" class="text-decoration-none">
                        <div class="card h-100 card-flyer hover-actions-trigger shadow-sm border-translucent">
                            <div class="card-body d-flex align-items-center">
                                <div class="avatar avatar-xl me-3 flex-shrink-0">
                                    <div class="avatar-name rounded bg-warning-subtle text-warning d-flex flex-center">
                                        <span class="fas fa-credit-card fs-6"></span>
                                    </div>
                                </div>
                                <div>
                                    <h5 class="text-body-emphasis mb-1">FINANCEIRO</h5>
                                    <p class="text-body-tertiary fs-10 mb-0">Pagamentos pendentes, controle financeiro e
                                        comissões.</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endcan

            {{-- LOGÍSTICA --}}
            @can('access.logistics')
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                    <a href="{{ route('logistics.index') }}" class="text-decoration-none">
                        <div class="card h-100 card-flyer hover-actions-trigger shadow-sm border-translucent">
                            <div class="card-body d-flex align-items-center">
                                <div class="avatar avatar-xl me-3 flex-shrink-0">
                                    <div class="avatar-name rounded bg-info-subtle text-info d-flex flex-center">
                                        <span class="fas fa-truck fs-6"></span>
                                    </div>
                                </div>
                                <div>
                                    <h5 class="text-body-emphasis mb-1">LOGÍSTICA</h5>
                                    <p class="text-body-tertiary fs-10 mb-0">Separação, logística de produtos e importação.</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endcan

            {{-- PRODUTOS --}}
            @can('access.products')
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                    <a href="{{ route('products.index') }}" class="text-decoration-none">
                        <div class="card h-100 card-flyer hover-actions-trigger shadow-sm border-translucent">
                            <div class="card-body d-flex align-items-center">
                                <div class="avatar avatar-xl me-3 flex-shrink-0">
                                    <div class="avatar-name rounded bg-danger-subtle text-danger d-flex flex-center">
                                        <span class="fas fa-box fs-6"></span>
                                    </div>
                                </div>
                                <div>
                                    <h5 class="text-body-emphasis mb-1">PRODUTOS</h5>
                                    <p class="text-body-tertiary fs-10 mb-0">Cadastro de produtos, bônus e promoções.</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endcan

            {{-- SAC --}}
            @can('access.supports')
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                    <a href="{{ route('supports.index') }}" class="text-decoration-none">
                        <div class="card h-100 card-flyer hover-actions-trigger shadow-sm border-translucent">
                            <div class="card-body d-flex align-items-center">
                                <div class="avatar avatar-xl me-3 flex-shrink-0">
                                    <div class="avatar-name rounded bg-secondary-subtle text-secondary d-flex flex-center">
                                        <span class="fas fa-headset fs-6"></span>
                                    </div>
                                </div>
                                <div>
                                    <h5 class="text-body-emphasis mb-1">SAC</h5>
                                    <p class="text-body-tertiary fs-10 mb-0">Assistência técnica e satisfação do cliente.</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endcan

        </div>
    </div>

    <style>
        .card-flyer:hover {
            transform: translateY(-4px);
            transition: all 0.3s ease;
            border-color: var(--phoenix-primary) !important;
            box-shadow: var(--phoenix-box-shadow-sm);
        }

        .card-flyer {
            transition: all 0.3s ease;
        }
    </style>
@endsection
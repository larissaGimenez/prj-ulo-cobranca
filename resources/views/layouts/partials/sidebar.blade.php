<nav class="navbar navbar-vertical navbar-expand-lg">
    <script>
        var navbarStyle = window.config.config.phoenixNavbarStyle;
        if (navbarStyle && navbarStyle !== 'transparent') {
            document.querySelector('body').classList.add(`navbar-${navbarStyle}`);
        }
    </script>
    <div class="collapse navbar-collapse" id="navbarVerticalCollapse">
        <div class="navbar-vertical-content">
            <ul class="navbar-nav flex-column" id="navbarVerticalNav">

                <li class="nav-item">
                    <div class="nav-item-wrapper">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }} label-1"
                            href="{{ route('dashboard') }}" role="button">
                            <div class="d-flex align-items-center">
                                <div class="dropdown-indicator-icon-wrapper">
                                    <span class="fas fa-caret-right dropdown-indicator-icon d-none"></span>
                                </div>
                                <span class="nav-link-icon"><span data-feather="pie-chart"></span></span>
                                <span class="nav-link-text-wrapper"><span class="nav-link-text">Dashboard</span></span>
                            </div>
                        </a>
                    </div>
                </li>

                <li class="nav-item">
                    <p class="navbar-vertical-label">Apps</p>
                    <hr class="navbar-vertical-line" />

                    @can('access.billings')
                        <div class="nav-item-wrapper">
                            <a class="nav-link {{ request()->routeIs('billings.*') ? 'active' : '' }} label-1"
                                href="{{ route('billings.index') }}" role="button">
                                <div class="d-flex align-items-center">
                                    <div class="dropdown-indicator-icon-wrapper"><span
                                            class="fas fa-caret-right dropdown-indicator-icon d-none"></span></div>
                                    <span class="nav-link-icon"><span data-feather="dollar-sign"></span></span>
                                    <span class="nav-link-text-wrapper"><span class="nav-link-text">Cobrança</span></span>
                                </div>
                            </a>
                        </div>
                    @endcan

                    @can('access.users')
                        <div class="nav-item-wrapper">
                            <a class="nav-link {{ request()->routeIs('cadastros.*') ? 'active' : '' }} label-1"
                                href="{{ route('users.index') }}" role="button">
                                <div class="d-flex align-items-center">
                                    <div class="dropdown-indicator-icon-wrapper"><span
                                            class="fas fa-caret-right dropdown-indicator-icon d-none"></span></div>
                                    <span class="nav-link-icon"><span data-feather="user-check"></span></span>
                                    <span class="nav-link-text-wrapper"><span class="nav-link-text">Cadastros</span></span>
                                </div>
                            </a>
                        </div>
                    @endcan

                    @can('access.sales')
                        <div class="nav-item-wrapper">
                            <a class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }} label-1"
                                href="{{ route('sales.index') }}" role="button">
                                <div class="d-flex align-items-center">
                                    <div class="dropdown-indicator-icon-wrapper"><span
                                            class="fas fa-caret-right dropdown-indicator-icon d-none"></span></div>
                                    <span class="nav-link-icon"><span data-feather="trending-up"></span></span>
                                    <span class="nav-link-text-wrapper"><span class="nav-link-text">Comercial</span></span>
                                </div>
                            </a>
                        </div>
                    @endcan

                    @can('access.finances')
                        <div class="nav-item-wrapper">
                            <a class="nav-link {{ request()->routeIs('finances.*') ? 'active' : '' }} label-1"
                                href="{{ route('finances.index') }}" role="button">
                                <div class="d-flex align-items-center">
                                    <div class="dropdown-indicator-icon-wrapper"><span
                                            class="fas fa-caret-right dropdown-indicator-icon d-none"></span></div>
                                    <span class="nav-link-icon"><span data-feather="credit-card"></span></span>
                                    <span class="nav-link-text-wrapper"><span class="nav-link-text">Financeiro</span></span>
                                </div>
                            </a>
                        </div>
                    @endcan

                    @can('access.logistics')
                        <div class="nav-item-wrapper">
                            <a class="nav-link {{ request()->routeIs('logistics.*') ? 'active' : '' }} label-1"
                                href="{{ route('logistics.index') }}" role="button">
                                <div class="d-flex align-items-center">
                                    <div class="dropdown-indicator-icon-wrapper"><span
                                            class="fas fa-caret-right dropdown-indicator-icon d-none"></span></div>
                                    <span class="nav-link-icon"><span data-feather="truck"></span></span>
                                    <span class="nav-link-text-wrapper"><span class="nav-link-text">Logística</span></span>
                                </div>
                            </a>
                        </div>
                    @endcan

                    @can('access.products')
                        <div class="nav-item-wrapper">
                            <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }} label-1"
                                href="{{ route('products.index') }}" role="button">
                                <div class="d-flex align-items-center">
                                    <div class="dropdown-indicator-icon-wrapper"><span
                                            class="fas fa-caret-right dropdown-indicator-icon d-none"></span></div>
                                    <span class="nav-link-icon"><span data-feather="box"></span></span>
                                    <span class="nav-link-text-wrapper"><span class="nav-link-text">Produtos</span></span>
                                </div>
                            </a>
                        </div>
                    @endcan

                    @can('access.supports')
                        <div class="nav-item-wrapper">
                            <a class="nav-link {{ request()->routeIs('supports.*') ? 'active' : '' }} label-1"
                                href="{{ route('supports.index') }}" role="button">
                                <div class="d-flex align-items-center">
                                    <div class="dropdown-indicator-icon-wrapper"><span
                                            class="fas fa-caret-right dropdown-indicator-icon d-none"></span></div>
                                    <span class="nav-link-icon"><span data-feather="headphones"></span></span>
                                    <span class="nav-link-text-wrapper"><span class="nav-link-text">SAC</span></span>
                                </div>
                            </a>
                        </div>
                    @endcan
                </li>

                <li class="nav-item">
                    <p class="navbar-vertical-label">Gestão</p>
                    <hr class="navbar-vertical-line" />

                    @can('access.users')
                        <div class="nav-item-wrapper">
                            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }} label-1"
                                href="{{ route('users.index') }}" role="button">
                                <div class="d-flex align-items-center">
                                    <div class="dropdown-indicator-icon-wrapper"><span
                                            class="fas fa-caret-right dropdown-indicator-icon d-none"></span></div>
                                    <span class="nav-link-icon"><span data-feather="users"></span></span>
                                    <span class="nav-link-text-wrapper"><span class="nav-link-text">Usuários</span></span>
                                </div>
                            </a>
                        </div>
                    @endcan

                    @can('access.roles')
                        <div class="nav-item-wrapper">
                            <a class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }} label-1"
                                href="{{ route('roles.index') }}" role="button">
                                <div class="d-flex align-items-center">
                                    <div class="dropdown-indicator-icon-wrapper"><span
                                            class="fas fa-caret-right dropdown-indicator-icon d-none"></span></div>
                                    <span class="nav-link-icon"><span data-feather="lock"></span></span>
                                    <span class="nav-link-text-wrapper"><span class="nav-link-text">Perfil de
                                            acesso</span></span>
                                </div>
                            </a>
                        </div>
                    @endcan

                    @can('access.credentials')
                        <div class="nav-item-wrapper">
                            <a class="nav-link {{ request()->routeIs('credentials.*') ? 'active' : '' }} label-1"
                                href="{{ route('admin.credentials.index') }}" role="button">
                                <div class="d-flex align-items-center">
                                    <div class="dropdown-indicator-icon-wrapper"><span
                                            class="fas fa-caret-right dropdown-indicator-icon d-none"></span></div>
                                    <span class="nav-link-icon"><span data-feather="lock"></span></span>
                                    <span class="nav-link-text-wrapper"><span
                                            class="nav-link-text">Credenciais</span></span>
                                </div>
                            </a>
                        </div>
                    @endcan
                </li>
            </ul>
        </div>
    </div>

    <div class="navbar-vertical-footer">
        <button
            class="btn navbar-vertical-toggle border-0 fw-semibold w-100 white-space-nowrap d-flex align-items-center">
            <span class="uil uil-left-arrow-to-left fs-8"></span>
            <span class="uil uil-arrow-from-right fs-8"></span>
            <span class="navbar-vertical-footer-text ms-2">Colapsar</span>
        </button>
    </div>
</nav>
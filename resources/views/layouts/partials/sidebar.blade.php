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
                            href="{{ route('dashboard') }}" role="button" data-bs-toggle="" aria-expanded="false">
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

                    <div class="nav-item-wrapper">
                        <a class="nav-link {{ request()->routeIs('cobranca.*') ? 'active' : '' }} label-1" href="#"
                            role="button">
                            <div class="d-flex align-items-center">
                                <div class="dropdown-indicator-icon-wrapper"><span
                                        class="fas fa-caret-right dropdown-indicator-icon d-none"></span></div>
                                <span class="nav-link-icon"><span data-feather="dollar-sign"></span></span>
                                <span class="nav-link-text-wrapper"><span class="nav-link-text">Cobrança</span></span>
                            </div>
                        </a>
                    </div>

                    <div class="nav-item-wrapper">
                        <a class="nav-link {{ request()->routeIs('cadastros.*') ? 'active' : '' }} label-1" href="#"
                            role="button">
                            <div class="d-flex align-items-center">
                                <div class="dropdown-indicator-icon-wrapper"><span
                                        class="fas fa-caret-right dropdown-indicator-icon d-none"></span></div>
                                <span class="nav-link-icon"><span data-feather="user-check"></span></span>
                                <span class="nav-link-text-wrapper"><span class="nav-link-text">Cadastros</span></span>
                            </div>
                        </a>
                    </div>

                    <div class="nav-item-wrapper">
                        <a class="nav-link {{ request()->routeIs('comercial.*') ? 'active' : '' }} label-1" href="#"
                            role="button">
                            <div class="d-flex align-items-center">
                                <div class="dropdown-indicator-icon-wrapper"><span
                                        class="fas fa-caret-right dropdown-indicator-icon d-none"></span></div>
                                <span class="nav-link-icon"><span data-feather="trending-up"></span></span>
                                <span class="nav-link-text-wrapper"><span class="nav-link-text">Comercial</span></span>
                            </div>
                        </a>
                    </div>

                    <div class="nav-item-wrapper">
                        <a class="nav-link {{ request()->routeIs('financeiro.*') ? 'active' : '' }} label-1" href="#"
                            role="button">
                            <div class="d-flex align-items-center">
                                <div class="dropdown-indicator-icon-wrapper"><span
                                        class="fas fa-caret-right dropdown-indicator-icon d-none"></span></div>
                                <span class="nav-link-icon"><span data-feather="credit-card"></span></span>
                                <span class="nav-link-text-wrapper"><span class="nav-link-text">Financeiro</span></span>
                            </div>
                        </a>
                    </div>

                    <div class="nav-item-wrapper">
                        <a class="nav-link {{ request()->routeIs('logistica.*') ? 'active' : '' }} label-1" href="#"
                            role="button">
                            <div class="d-flex align-items-center">
                                <div class="dropdown-indicator-icon-wrapper"><span
                                        class="fas fa-caret-right dropdown-indicator-icon d-none"></span></div>
                                <span class="nav-link-icon"><span data-feather="truck"></span></span>
                                <span class="nav-link-text-wrapper"><span class="nav-link-text">Logística</span></span>
                            </div>
                        </a>
                    </div>

                    <div class="nav-item-wrapper">
                        <a class="nav-link {{ request()->routeIs('produtos.*') ? 'active' : '' }} label-1" href="#"
                            role="button">
                            <div class="d-flex align-items-center">
                                <div class="dropdown-indicator-icon-wrapper"><span
                                        class="fas fa-caret-right dropdown-indicator-icon d-none"></span></div>
                                <span class="nav-link-icon"><span data-feather="box"></span></span>
                                <span class="nav-link-text-wrapper"><span class="nav-link-text">Produtos</span></span>
                            </div>
                        </a>
                    </div>

                    <div class="nav-item-wrapper">
                        <a class="nav-link {{ request()->routeIs('sac.*') ? 'active' : '' }} label-1" href="#"
                            role="button">
                            <div class="d-flex align-items-center">
                                <div class="dropdown-indicator-icon-wrapper"><span
                                        class="fas fa-caret-right dropdown-indicator-icon d-none"></span></div>
                                <span class="nav-link-icon"><span data-feather="headphones"></span></span>
                                <span class="nav-link-text-wrapper"><span class="nav-link-text">SAC</span></span>
                            </div>
                        </a>
                    </div>
                </li>

                <li class="nav-item">
                    <p class="navbar-vertical-label">Gestão</p>
                    <hr class="navbar-vertical-line" />

                    <div class="nav-item-wrapper">
                        <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }} label-1"
                            href="{{ route('users.index') }}" role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center">
                                <div class="dropdown-indicator-icon-wrapper">
                                    <span class="fas fa-caret-right dropdown-indicator-icon d-none"></span>
                                </div>
                                <span class="nav-link-icon"><span data-feather="users"></span></span>
                                <span class="nav-link-text-wrapper"><span class="nav-link-text">Usuários</span></span>
                            </div>
                        </a>
                    </div>

                    <div class="nav-item-wrapper">
                        <a class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }} label-1" href="#"
                            role="button" data-bs-toggle="" aria-expanded="false">
                            <div class="d-flex align-items-center">
                                <div class="dropdown-indicator-icon-wrapper">
                                    <span class="fas fa-caret-right dropdown-indicator-icon d-none"></span>
                                </div>
                                <span class="nav-link-icon"><span data-feather="lock"></span></span>
                                <span class="nav-link-text-wrapper"><span class="nav-link-text">Perfil de
                                        acesso</span></span>
                            </div>
                        </a>
                    </div>

                    <div class="nav-item-wrapper">
                        <a class="nav-link {{ request()->routeIs('admin.credentials.*') ? 'active' : '' }} label-1"
                            href="{{ route('admin.credentials.index') }}">
                            <div class="d-flex align-items-center">
                                <span class="nav-link-icon"><span data-feather="key"></span></span>
                                <span class="nav-link-text-wrapper"><span class="nav-link-text">Credenciais
                                        API</span></span>
                            </div>
                        </a>
                    </div>
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
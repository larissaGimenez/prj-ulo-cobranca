@extends('layouts.app')

@section('content')
    <nav class="mb-3" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.tenants.index') }}">Tenants</a></li>
            <li class="breadcrumb-item active">Visualizar Aplicativo</li>
        </ol>
    </nav>

    <div class="mb-9">
        @if(session('success'))
            <div class="alert alert-subtle-success mb-4" role="alert">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-subtle-danger mb-4" role="alert">{{ session('error') }}</div>
        @endif

        <div class="row g-3 flex-between-center mb-5">
            <div class="col-auto">
                <h2 class="mb-2">Detalhes do Aplicativo: {{ $tenant->name }}</h2>
                <h5 class="text-body-tertiary fw-semibold">Informações de integração e credenciais de acesso ao ERP.</h5>
            </div>
            <div class="col-auto">
                <a class="btn btn-phoenix-success me-2" href="{{ route('admin.tenants.test-connection', $tenant) }}">
                    <span class="fas fa-plug me-2"></span>Testar Conexão
                </a>
                <a class="btn btn-phoenix-primary me-2" href="{{ route('admin.tenants.edit', $tenant) }}">
                    <span class="fas fa-edit me-2"></span>Editar Dados
                </a>
                <a class="btn btn-phoenix-secondary" href="{{ route('admin.tenants.index') }}">Voltar</a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-xl-8">
                <div class="card shadow-none border border-300">
                    <div class="card-header border-bottom border-300 bg-body-tertiary">
                        <h5 class="text-body-highlight mb-0">Informações da Integração</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6">
                                <h6 class="text-body-tertiary mb-1">Nome do Aplicativo</h6>
                                <p class="text-body-highlight fw-bold mb-0 text-uppercase">{{ $tenant->name }}</p>
                            </div>
                            <div class="col-12 col-sm-6">
                                <h6 class="text-body-tertiary mb-1">Sistema Conectado</h6>
                                <p class="mb-0 fw-bold text-uppercase">
                                    <span class="fas fa-link text-primary me-2"></span>{{ $tenant->driver }}
                                </p>
                            </div>
                            <div class="col-12 col-sm-6">
                                <h6 class="text-body-tertiary mb-1">Status da Conexão</h6>
                                <span class="badge badge-phoenix badge-phoenix-{{ $tenant->is_active ? 'success' : 'danger' }}">
                                    {{ $tenant->is_active ? 'Ativo / Recebendo Dados' : 'Inativo / Pausado' }}
                                </span>
                            </div>
                            <div class="col-12 col-sm-6">
                                <h6 class="text-body-tertiary mb-1">Data de Cadastro</h6>
                                <p class="text-body-highlight mb-0 fw-semibold">{{ $tenant->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-none border border-300 mt-4">
                    <div class="card-header border-bottom border-300 bg-body-tertiary">
                        <h5 class="text-body-highlight mb-0">Credenciais Técnicas</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fs-9 text-uppercase text-body-tertiary">App Key</label>
                            <div class="input-group">
                                <input class="form-control bg-body-highlight font-mono fs-9" type="text" value="{{ $tenant->app_key }}" readonly />
                                <button class="btn btn-phoenix-secondary px-3" onclick="copyToClipboard('{{ $tenant->app_key }}')">
                                    <span class="fas fa-copy"></span>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="form-label fs-9 text-uppercase text-body-tertiary">App Secret</label>
                            <div class="input-group">
                                <input class="form-control bg-body-highlight font-mono fs-9" id="secretField" type="password" value="{{ $tenant->app_secret }}" readonly />
                                <button class="btn btn-phoenix-secondary px-3" onclick="toggleSecret()">
                                    <span class="fas fa-eye" id="eyeIcon"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="card shadow-none border border-300">
                    <div class="card-header border-bottom border-300 bg-body-tertiary text-center">
                        <h5 class="text-body-highlight mb-0">Configurações Adicionais</h5>
                    </div>
                    <div class="card-body">
                        @if($tenant->settings)
                             <pre class="bg-body-highlight p-3 rounded-3 fs-10 scrollbar" style="max-height: 200px;"><code>{{ json_encode($tenant->settings, JSON_PRETTY_PRINT) }}</code></pre>
                        @else
                            <p class="text-center text-body-tertiary my-3 fs-9">Nenhuma configuração customizada definida em JSON.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function toggleSecret() {
                const input = document.getElementById('secretField');
                const icon = document.getElementById('eyeIcon');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.replace('fa-eye', 'fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.replace('fa-eye-slash', 'fa-eye');
                }
            }
            function copyToClipboard(text) {
                navigator.clipboard.writeText(text);
                alert('App Key copiada!');
            }
        </script>
    @endpush
@endsection
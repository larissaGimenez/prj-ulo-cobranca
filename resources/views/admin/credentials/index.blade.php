@extends('layouts.app')

@section('content')
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Credenciais API</li>
    </ol>
</nav>

<div class="mb-5">
    <h2 class="text-bold text-body-emphasis mb-2">Credenciais API</h2>
    <p class="text-body-tertiary lh-sm mb-0">Gerencie chaves de acesso para conexões externas.</p>
</div>

<div id="credentialsTable" data-list='{"valueNames":["name","id"],"page":10,"pagination":true}'>
    <div class="row align-items-center justify-content-between g-3 mb-4">
        <div class="col col-auto">
            <div class="search-box">
                <form class="position-relative">
                    <input class="form-control search-input search" type="search" placeholder="Buscar credencial" aria-label="Search" />
                    <span class="fas fa-search search-box-icon"></span>
                </form>
            </div>
        </div>
        <div class="col-auto">
            <div class="d-flex align-items-center">
                <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#modalCreateCredential">
                    <span class="fas fa-plus me-2"></span>Gerar Nova Credencial
                </button>
            </div>
        </div>
    </div>

    <div class="mx-n4 mx-lg-n6 px-4 px-lg-6 mb-9 bg-body-emphasis border-y mt-2 position-relative top-1">
        <div class="table-responsive scrollbar ms-n1 ps-1">
            <table class="table table-sm fs-9 mb-0">
                <thead>
                    <tr>
                        <th class="sort align-middle ps-0" scope="col" data-sort="name" style="width:30%;">NOME DA INTEGRAÇÃO</th>
                        <th class="sort align-middle" scope="col" data-sort="id" style="width:40%;">CLIENT ID</th>
                        <th class="sort align-middle" scope="col" style="width:20%;">SECRET</th>
                        <th class="sort align-middle text-end pe-0" scope="col">AÇÕES</th>
                    </tr>
                </thead>
                <tbody class="list">
                    @forelse($credentials as $client)
                        <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                            <td class="align-middle white-space-nowrap ps-0 name">
                                <h6 class="mb-0 fw-semibold text-body-highlight">{{ $client->name }}</h6>
                            </td>
                            <td class="align-middle white-space-nowrap id">
                                <code>{{ $client->id }}</code>
                            </td>
                            <td class="align-middle white-space-nowrap">
                                <span class="badge badge-phoenix badge-phoenix-secondary">****************</span>
                            </td>
                            <td class="align-middle text-end white-space-nowrap pe-0">
                                <button class="btn btn-sm btn-phoenix-danger" type="button" 
                                    data-bs-toggle="modal" data-bs-target="#modalConfirmDelete" 
                                    data-id="{{ $client->id }}" data-name="{{ $client->name }}">
                                    <span class="fas fa-trash-alt"></span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-body-tertiary">Nenhuma credencial configurada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="row align-items-center justify-content-between py-2 pe-0 fs-9">
            <div class="col-auto d-flex">
                <p class="mb-0 d-none d-sm-block me-3 fw-semibold text-body" data-list-info="data-list-info"></p>
            </div>
            <div class="col-auto d-flex">
                <button class="page-link" data-list-pagination="prev"><span class="fas fa-chevron-left"></span></button>
                <ul class="mb-0 pagination"></ul>
                <button class="page-link pe-0" data-list-pagination="next"><span class="fas fa-chevron-right"></span></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCreateCredential" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-translucent shadow-lg">
            
            <div id="modalStepForm">
                <form id="formCreateCredential" onsubmit="criarCredencial(event)">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Gerar Nova Credencial</h5>
                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="inputContainer">
                            <div class="mb-3">
                                <label class="form-label">Nome da Integração</label>
                                <input class="form-control" name="name" id="inputName" type="text" placeholder="Ex: n8n Workflow" maxlength="50" required />
                                <div id="nameErrorFeedback" class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div id="processMessage" class="d-none text-center py-4">
                            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
                            <h5 class="text-primary fw-bold mb-0">Gerando chaves de segurança...</h5>
                            <p class="text-body-tertiary fs-10">Isso pode levar alguns segundos.</p>
                        </div>
                    </div>
                    <div class="modal-footer" id="footerForm">
                        <button class="btn btn-link text-danger px-3 my-0" type="button" data-bs-dismiss="modal">Cancelar</button>
                        <button class="btn btn-primary" type="submit" id="btnSubmit">Gerar Credencial</button>
                    </div>
                </form>
            </div>

            <div id="modalStepResult" class="d-none">
                <div class="modal-header bg-primary-subtle">
                    <h5 class="modal-title text-primary">✓ Credencial Gerada!</h5>
                </div>
                <div class="modal-body">
                    <div class="alert alert-subtle-danger d-flex align-items-center mb-4" role="alert">
                        <div class="p-2">
                            <span class="fas fa-exclamation-triangle fs-5 text-danger"></span>
                        </div>
                        <div class="flex-1 ms-3">
                            <h6 class="mb-1 text-danger-emphasis fw-bold text-uppercase">Atenção: Copie sua chave agora!</h6>
                            <p class="mb-0 fs-9 text-danger-emphasis">
                                Por questões de segurança, esta chave <strong>não será exibida novamente</strong> após fechar esta tela. 
                                Se você a perder, precisará excluir esta credencial e gerar uma nova.
                            </p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fs-10 text-uppercase">Client ID</label>
                        <div class="input-group">
                            <input type="text" class="form-control bg-light" id="resClientId" readonly>
                            <button class="btn btn-phoenix-primary px-3 copy-btn" type="button" onclick="copyToClipboard('resClientId', this)">
                                <span class="fas fa-copy"></span>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fs-10 text-uppercase text-danger">Client Secret</label>
                        <div class="input-group">
                            <input type="text" class="form-control bg-light fw-bold text-danger" id="resClientSecret" readonly>
                            <button class="btn btn-phoenix-danger px-3 copy-btn" type="button" onclick="copyToClipboard('resClientSecret', this)">
                                <span class="fas fa-copy"></span>
                            </button>
                        </div>
                    </div>
                    <button class="btn btn-primary w-100 mt-3" type="button" onclick="window.location.reload();">Fechar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalConfirmDelete" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-translucent">
            <div class="modal-header border-0 pb-0">
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div id="deleteStepForm">
                <form id="formDeleteCredential" onsubmit="deletarCredencial(event)">
                    @csrf
                    <div class="modal-body text-center">
                        <span class="fas fa-exclamation-triangle text-danger fs-1 mb-3"></span>
                        <h4 class="mb-2">Excluir?</h4>
                        <p class="text-body-tertiary mb-3">Confirme sua senha para remover <br><strong id="deleteCredentialName" class="text-body-highlight"></strong></p>
                        
                        <div class="text-start">
                            <label class="form-label fs-10 text-uppercase">Sua Senha</label>
                            <input type="password" name="password" id="deletePassword" class="form-control form-control-sm" placeholder="Digite sua senha" required>
                            <div id="errorDeletePassword" class="text-danger fs-10 mt-1 d-none">Senha incorreta!</div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 justify-content-center">
                        <button class="btn btn-link text-danger px-3" type="button" data-bs-dismiss="modal">Cancelar</button>
                        <button class="btn btn-danger" type="submit" id="btnConfirmDelete">Confirmar Exclusão</button>
                    </div>
                </form>
            </div>

            <div id="deleteStepProcess" class="d-none text-center py-5 px-3">
                <div id="deleteSpinnerContent">
                    <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
                    <h5 class="text-primary fw-bold mb-0">Validando exclusão...</h5>
                </div>

                <div id="deleteSuccessContent" class="d-none">
                    <div class="text-success mb-3">
                        <span class="fas fa-check-circle" style="font-size: 3rem;"></span>
                    </div>
                    <h5 class="text-success fw-bold mb-0">Excluído com sucesso!</h5>
                    <p class="text-body-tertiary fs-10 mb-0">A página será atualizada.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // --- 1. FUNÇÃO GLOBAL DE COPIAR ---
    function copyToClipboard(inputId, btn) {
        const input = document.getElementById(inputId);
        input.select();
        input.setSelectionRange(0, 99999);

        navigator.clipboard.writeText(input.value).then(() => {
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<span class="fas fa-check"></span>';
            btn.classList.replace('btn-phoenix-primary', 'btn-success');
            btn.classList.replace('btn-phoenix-danger', 'btn-success');
            
            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.classList.remove('btn-success');
                if(inputId === 'resClientId') btn.classList.add('btn-phoenix-primary');
                else btn.classList.add('btn-phoenix-danger');
            }, 2000);
        });
    }

    // --- 2. VARIÁVEL GLOBAL PARA EXCLUSÃO ---
    let credentialIdToDelete = null;

    // --- 3. FUNÇÃO PARA CRIAR CREDENCIAL ---
    async function criarCredencial(event) {
        event.preventDefault(); 

        const btnSubmit = document.getElementById('btnSubmit');
        const inputName = document.getElementById('inputName');
        const errorFeedback = document.getElementById('nameErrorFeedback');
        
        // Elementos de transição
        const modalStepForm = document.getElementById('modalStepForm');
        const processMsg = document.getElementById('processMessage');
        const inputContainer = document.getElementById('inputContainer');
        const footerForm = document.getElementById('footerForm');

        // --- VALIDAÇÃO LOCAL ANTES DO SPINNER ---
        if (inputName.value.trim().length < 3) {
            inputName.classList.add('is-invalid');
            errorFeedback.textContent = "O nome deve ter pelo menos 3 caracteres.";
            return;
        }

        // Limpa estados de erro anteriores e inicia carregamento
        inputName.classList.remove('is-invalid');
        btnSubmit.disabled = true;
        
        // Esconde o input e mostra o spinner
        inputContainer.classList.add('d-none');
        footerForm.classList.add('d-none');
        processMsg.classList.remove('d-none');

        try {
            const response = await fetch("{{ route('admin.credentials.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ name: inputName.value })
            });

            const data = await response.json();

            if (!response.ok) {
                // Se o erro for de validação (nome duplicado)
                if (response.status === 422) {
                    throw new Error(data.errors?.name ? data.errors.name[0] : data.message);
                }
                throw new Error(data.message || 'Erro no servidor.');
            }

            // Sucesso: Preenche e mostra resultado
            document.getElementById('resClientId').value = data.client_id || data.id;
            document.getElementById('resClientSecret').value = data.client_secret || data.secret;
            modalStepForm.classList.add('d-none');
            document.getElementById('modalStepResult').classList.remove('d-none');

        } catch (error) {
            // --- VOLTA PARA O FORMULÁRIO CASO DÊ ERRO ---
            processMsg.classList.add('d-none');
            inputContainer.classList.remove('d-none');
            footerForm.classList.remove('d-none');
            btnSubmit.disabled = false;
            inputName.disabled = false;

            // Aplica o erro no campo
            inputName.classList.add('is-invalid');
            errorFeedback.textContent = error.message;
            inputName.focus();
        }
    }

    // --- 4. FUNÇÃO PARA DELETAR CREDENCIAL ---
    async function deletarCredencial(event) {
        event.preventDefault();

        const passwordInput = document.getElementById('deletePassword');
        const errorDiv = document.getElementById('errorDeletePassword');
        
        // Elementos de transição
        const stepForm = document.getElementById('deleteStepForm');
        const stepProcess = document.getElementById('deleteStepProcess');
        const spinnerContent = document.getElementById('deleteSpinnerContent');
        const successContent = document.getElementById('deleteSuccessContent');

        // 1. Inicia transição para o Spinner
        stepForm.classList.add('d-none');
        stepProcess.classList.remove('d-none');
        spinnerContent.classList.remove('d-none');
        successContent.classList.add('d-none');
        errorDiv.classList.add('d-none');
        passwordInput.classList.remove('is-invalid');

        try {
            let url = "{{ route('admin.credentials.destroy', ':id') }}".replace(':id', credentialIdToDelete);

            const response = await fetch(url, {
                method: "DELETE",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ password: passwordInput.value })
            });

            if (response.status === 403 || response.status === 422) {
                // Se a senha estiver errada, volta para o formulário
                stepProcess.classList.add('d-none');
                stepForm.classList.remove('d-none');
                errorDiv.classList.remove('d-none');
                passwordInput.classList.add('is-invalid');
                passwordInput.focus();
            } else if (!response.ok) {
                throw new Error('Erro ao processar exclusão.');
            } else {
                // SUCESSO: Troca Spinner por Check de Sucesso
                spinnerContent.classList.add('d-none');
                successContent.classList.remove('d-none');

                // Aguarda 1.5 segundos para o usuário ver o check e recarrega
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            }

        } catch (error) {
            alert(error.message);
            // Em caso de erro crítico, volta ao formulário
            stepProcess.classList.add('d-none');
            stepForm.classList.remove('d-none');
        }
    }

    // --- 5. INICIALIZAÇÃO DOS LISTENERS ---
    document.addEventListener('DOMContentLoaded', function() {
        const modalConfirmDelete = document.getElementById('modalConfirmDelete');
        if (modalConfirmDelete) {
            modalConfirmDelete.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                credentialIdToDelete = button.getAttribute('data-id');
                const credentialName = button.getAttribute('data-name');
                
                document.getElementById('deleteCredentialName').textContent = credentialName;
                document.getElementById('deletePassword').value = '';
                document.getElementById('errorDeletePassword').classList.add('d-none');
                document.getElementById('deletePassword').classList.remove('is-invalid');
            });
        }
    });
</script>
@endpush
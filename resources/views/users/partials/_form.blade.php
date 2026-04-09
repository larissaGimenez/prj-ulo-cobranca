<div class="row g-3">
    <div class="col-12 col-sm-6">
        <label class="form-label" for="name">Nome Completo</label>
        <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text"
            placeholder="Ex: Larissa Manzano" value="{{ old('name', $user->name ?? '') }}" required />
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-sm-6">
        <label class="form-label" for="email">E-mail</label>
        <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email"
            placeholder="nome@exemplo.com" value="{{ old('email', $user->email ?? '') }}" required />
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-sm-6">
        <label class="form-label" for="doc_type">Tipo de Pessoa</label>
        <select class="form-select" id="doc_type" onchange="handleDocTypeChange()">
            <option value="cpf" {{ old('cpf', $user->cpf ?? '') ? 'selected' : '' }}>Pessoa Física (CPF)</option>
            <option value="cnpj" {{ old('cnpj', $user->cnpj ?? '') ? 'selected' : '' }}>Pessoa Jurídica (CNPJ)</option>
        </select>
    </div>

    <div class="col-12 col-sm-6" id="group-cpf">
        <label class="form-label" for="cpf">CPF</label>
        <input class="form-control @error('cpf') is-invalid @enderror" id="cpf" name="cpf" type="text"
            placeholder="000.000.000-00" maxlength="14" oninput="maskCPF(this)"
            value="{{ old('cpf', $user->cpf ?? '') }}" />
        @error('cpf')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-sm-6 d-none" id="group-cnpj">
        <label class="form-label" for="cnpj">CNPJ</label>
        <input class="form-control @error('cnpj') is-invalid @enderror" id="cnpj" name="cnpj" type="text"
            placeholder="00.000.000/0000-00" maxlength="18" oninput="maskCNPJ(this)"
            value="{{ old('cnpj', $user->cnpj ?? '') }}" />
        @error('cnpj')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-sm-4">
        <label class="form-label" for="phone">Contato (Telefone)</label>
        <input class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" type="text"
            placeholder="(00) 00000-0000" maxlength="15" oninput="maskPhone(this)"
            value="{{ old('phone', $user->phone ?? '') }}" />
        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-sm-4">
        <label class="form-label" for="role">Função / Perfil</label>
        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
            <option value="">Selecione...</option>
            @foreach($roles as $role)
                <option value="{{ $role->name }}" {{ (old('role') == $role->name || (isset($user) && $user->hasRole($role->name))) ? 'selected' : '' }}>
                    {{ ucfirst($role->name) }}
                </option>
            @endforeach
        </select>
        @error('role')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-sm-4">
        <label class="form-label" for="status">Status</label>
        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
            <option value="active" {{ old('status', $user->status ?? 'active') == 'active' ? 'selected' : '' }}>Ativo
            </option>
            <option value="pending" {{ old('status', $user->status ?? '') == 'pending' ? 'selected' : '' }}>Pendente
            </option>
            <option value="inactive" {{ old('status', $user->status ?? '') == 'inactive' ? 'selected' : '' }}>Inativo
            </option>
        </select>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    @isset($user)
        <hr class="my-4" />
        <div class="col-12 col-sm-6">
            <label class="form-label" for="password">Alterar Senha</label>
            <input class="form-control @error('password') is-invalid @enderror" id="password" name="password"
                type="password" placeholder="Deixe em branco para não alterar" />
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12 col-sm-6">
            <label class="form-label" for="password_confirmation">Confirmar Nova Senha</label>
            <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" />
        </div>
    @endisset
</div>

<script>
    function handleDocTypeChange() {
        const type = document.getElementById('doc_type').value;
        const gCpf = document.getElementById('group-cpf');
        const gCnpj = document.getElementById('group-cnpj');
        const iCpf = document.getElementById('cpf');
        const iCnpj = document.getElementById('cnpj');

        if (type === 'cpf') {
            gCpf.classList.remove('d-none');
            gCnpj.classList.add('d-none');
            // Não limpamos o valor aqui para não perder o que o user digitou se ele alternar sem querer
        } else {
            gCnpj.classList.remove('d-none');
            gCpf.classList.add('d-none');
        }
    }

    function maskCPF(i) {
        let v = i.value.replace(/\D/g, "");
        if (v.length <= 11) {
            v = v.replace(/(\d{3})(\d)/, "$1.$2");
            v = v.replace(/(\d{3})(\d)/, "$1.$2");
            v = v.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
        }
        i.value = v;
    }

    function maskCNPJ(i) {
        let v = i.value.replace(/\D/g, "");
        v = v.replace(/^(\d{2})(\d)/, "$1.$2");
        v = v.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3");
        v = v.replace(/\.(\d{3})(\d)/, ".$1/$2");
        v = v.replace(/(\d{4})(\d)/, "$1-$2");
        i.value = v;
    }

    function maskPhone(i) {
        let v = i.value.replace(/\D/g, "");
        v = v.replace(/^(\d{2})(\d)/g, "($1) $2");
        v = v.replace(/(\d{5})(\d)/, "$1-$2");
        i.value = v;
    }

    document.addEventListener('DOMContentLoaded', () => {
        handleDocTypeChange();

        // Aplicar máscara no carregamento se já houver valor (edição)
        const cpfInput = document.getElementById('cpf');
        const cnpjInput = document.getElementById('cnpj');
        const phoneInput = document.getElementById('phone');

        if (cpfInput.value) maskCPF(cpfInput);
        if (cnpjInput.value) maskCNPJ(cnpjInput);
        if (phoneInput.value) maskPhone(phoneInput);
    });
</script>
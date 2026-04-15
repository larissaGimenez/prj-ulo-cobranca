@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fs-9 shadow-none" for="name">Nome do Aplicativo (Ex: ULO.01)</label>
        <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" 
            placeholder="Digite o nome do app" value="{{ old('name', $tenant->name) }}" required />
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fs-9" for="driver">Sistema de Origem</label>
        <select class="form-select @error('driver') is-invalid @enderror" id="driver" name="driver">
            <option value="omie" {{ old('driver', $tenant->driver) == 'omie' ? 'selected' : '' }}>Omie ERP</option>
        </select>
        @error('driver')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fs-9" for="app_key">App Key</label>
        <input class="form-control @error('app_key') is-invalid @enderror" id="app_key" name="app_key" type="text" 
            placeholder="Insira a App Key" value="{{ old('app_key', $tenant->app_key) }}" required />
        @error('app_key')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fs-9" for="app_secret">
            App Secret {{ $tenant->exists ? '(Deixe vazio para manter)' : '' }}
        </label>
        <input class="form-control @error('app_secret') is-invalid @enderror" id="app_secret" name="app_secret" 
            type="password" placeholder="Insira o Secret" {{ $tenant->exists ? '' : 'required' }} />
        @error('app_secret')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 mt-4">
        <div class="form-check form-switch p-0">
            <input type="hidden" name="is_active" value="0">
            <input class="form-check-input ms-0 me-2" id="is_active" type="checkbox" name="is_active" 
                value="1" {{ old('is_active', $tenant->is_active ?? true) ? 'checked' : '' }} />
            <label class="form-check-label fw-bold text-body-highlight" for="is_active">O aplicativo está ativo para processamento?</label>
        </div>
    </div>
</div>

<div class="row g-3 mt-4">
    <div class="col-12 text-end">
        <a class="btn btn-link text-body px-5" href="{{ route('admin.tenants.index') }}">Cancelar</a>
        <button class="btn btn-primary px-7" type="submit">
            {{ $tenant->exists ? 'Atualizar Aplicativo' : 'Cadastrar Aplicativo' }}
        </button>
    </div>
</div>
<div class="row g-3">
    <div class="col-12">
        <label class="form-label" for="name">Nome do Perfil</label>
        <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text"
            placeholder="Ex: Gerente de Cobrança" value="{{ old('name', $role->name ?? '') }}" required />
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 mt-4">
        <h5 class="mb-3 text-body-highlight">Atribuir Permissões</h5>
        <div class="row g-3 p-3 border rounded-3 bg-light">
            @foreach($permissions as $permission)
                <div class="col-md-4 col-sm-6">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                            id="perm_{{ $permission->id }}" {{ (isset($role) && $role->hasPermissionTo($permission->name)) ? 'checked' : '' }}>
                        <label class="form-check-label cursor-pointer" for="perm_{{ $permission->id }}">
                            {{ $permission->name }}
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
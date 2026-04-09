@php
    // Mapeamento amigável para os nomes dos módulos
    $entityNames = [
        'users'       => 'Usuários',
        'roles'       => 'Perfis de Acesso',
        'billings'    => 'Cobranças',
        'sales'       => 'Comercial',
        'finances'    => 'Financeiro',
        'logistics'   => 'Logística',
        'products'    => 'Produtos',
        'supports'    => 'SAC / Suporte',
        'credentials' => 'Credenciais de API / Sistema', // <-- Nome amigável
    ];
@endphp

<div class="row g-3">
    <div class="col-12 mb-4">
        <label class="form-label fs-8 text-body-highlight ps-0 text-uppercase fw-bold" for="name">
            Nome do Perfil
        </label>
        <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text"
            placeholder="Ex: Gerente de Operações" value="{{ old('name', $role->name ?? '') }}" required />
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <div class="d-flex align-items-center mb-3">
            <span class="fa-solid fa-layer-group me-2 text-primary"></span>
            <h5 class="mb-0 text-body-highlight">Permissões de Acesso aos Módulos</h5>
        </div>

        <div class="row g-3">
            {{-- Loop nas permissões agrupadas pelo prefixo (access) enviadas pelo Controller --}}
            @isset($groupedByEntity)
                @foreach($groupedByEntity as $group => $items)
                    <div class="col-12">
                        <div class="card border border-300 shadow-none">
                            <div class="card-header bg-body-tertiary py-2 px-3">
                                <h6 class="mb-0 text-uppercase fs-10 text-body-highlight fw-bold">
                                    {{ $group === 'access' ? 'Módulos Disponíveis no Sistema' : 'Outros Acessos' }}
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="row">
                                    @foreach($items as $permission)
                                        @php
                                            // Extrai a entidade do nome (ex: de 'access.users' extrai 'users')
                                            $entitySlug = str_contains($permission->name, '.') 
                                                ? explode('.', $permission->name)[1] 
                                                : $permission->name;
                                        @endphp
                                        <div class="col-12 col-md-4 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input cursor-pointer" type="checkbox" 
                                                    name="permissions[]" 
                                                    value="{{ $permission->name }}"
                                                    id="perm_{{ $permission->id }}" 
                                                    {{ (isset($role) && $role->hasPermissionTo($permission->name)) ? 'checked' : '' }}>
                                                <label class="form-check-label cursor-pointer fs-9 text-body-highlight" for="perm_{{ $permission->id }}">
                                                    Habilitar Módulo: <strong>{{ $entityNames[$entitySlug] ?? ucfirst($entitySlug) }}</strong>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-12">
                    <div class="alert alert-subtle-warning">Nenhuma permissão de acesso foi localizada no banco de dados.</div>
                </div>
            @endisset
        </div>
    </div>
</div>

<style>
    .cursor-pointer { cursor: pointer; }
    .form-check-input:checked { 
        background-color: var(--phoenix-primary) !important; 
        border-color: var(--phoenix-primary) !important; 
    }
</style>
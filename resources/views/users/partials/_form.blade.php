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
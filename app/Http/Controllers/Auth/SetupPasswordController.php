<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class SetupPasswordController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {
    }

    /**
     * Exibe a tela de definição de senha.
     */
    public function create(Request $request, User $user): View|RedirectResponse
    {
        // Segurança extra: se o usuário já estiver ativo, manda pro login
        if ($user->status === 'active') {
            return redirect()->route('login')
                ->with('info', 'Sua senha já foi cadastrada anteriormente.');
        }

        return view('auth.set-password', compact('user'));
    }

    /**
     * Processa a nova senha do usuário.
     */
    public function store(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // 1. Atualiza a senha e muda o status para 'active'
        $user->update([
            'password' => Hash::make($request->password),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        return redirect()->route('login')
            ->with('status', 'Senha cadastrada com sucesso! Agora você pode acessar o sistema.');
    }
}
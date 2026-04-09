<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {
    }

    /**
     * Lista todos os usuários (incluindo deletados via Service)
     */
    public function index(): View
    {
        $users = $this->userService->getAllPaginated();

        return view('users.index', compact('users'));
    }

    /**
     * Exibe detalhes do usuário (suporta usuários na lixeira)
     */
    public function show(int $id): View
    {
        $user = User::withTrashed()->findOrFail($id);
        return view('users.show', compact('user'));
    }

    /**
     * Abre formulário de criação com objeto limpo para o partial
     */
    public function create(): View
    {
        $user = new User();
        $roles = Role::all();
        return view('users.create', compact('user', 'roles'));
    }

    /**
     * Salva novo usuário e dispara convite n8n
     */
    public function store(UserStoreRequest $request): RedirectResponse
    {
        $this->userService->create($request->validated());

        return redirect()->route('users.index')
            ->with('status', 'Usuário criado com sucesso e convite enviado!');
    }

    /**
     * Abre formulário de edição (suporta usuários na lixeira)
     */
    public function edit(int $id): View
    {
        $user = User::withTrashed()->findOrFail($id);
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Atualiza dados e sincroniza roles
     */
    public function update(UserUpdateRequest $request, int $id): RedirectResponse
    {
        $user = User::withTrashed()->findOrFail($id);
        $this->userService->update($user, $request->validated());

        return redirect()->route('users.index')
            ->with('status', 'Usuário atualizado com sucesso!');
    }

    /**
     * Remove usuário (Soft Delete)
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->userService->delete($user);

        return redirect()->route('users.index')
            ->with('status', 'Usuário desativado com sucesso (Soft Delete)!');
    }

    /**
     * Reenvia o convite para o Webhook do n8n
     */
    public function resendInvite(int $id): RedirectResponse
    {
        $user = User::withTrashed()->findOrFail($id);

        if ($user->status !== 'pending' || $user->trashed()) {
            return redirect()->back()
                ->with('error', 'Não é possível reenviar o convite para usuários ativos ou deletados.');
        }

        $this->userService->sendInvite($user);

        return redirect()->route('users.index')
            ->with('status', 'Convite reenviado com sucesso para ' . $user->email);
    }
}
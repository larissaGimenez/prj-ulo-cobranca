<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {
    }

    public function index(): View
    {
        $users = $this->userService->getAllPaginated();

        return view('users.index', compact('users'));
    }

    public function show(User $user): View
    {
        return view('users.show', compact('user'));
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function store(UserStoreRequest $request): RedirectResponse
    {
        $this->userService->create($request->validated());

        return redirect()->route('users.index')
            ->with('status', 'Usuário criado com sucesso!');
    }

    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        $this->userService->update($user, $request->validated());

        return redirect()->route('users.index')
            ->with('status', 'Usuário atualizado com sucesso!');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->userService->delete($user);

        return redirect()->route('users.index')
            ->with('status', 'Usuário removido (Soft Delete aplicado)!');
    }

    public function resendInvite(User $user, UserService $userService): RedirectResponse
    {
        if ($user->status !== 'pending' || $user->trashed()) {
            return redirect()->back()
                ->with('error', 'Este usuário já está ativo ou foi desativado.');
        }

        $userService->sendInvite($user);

        return redirect()->route('users.index')
            ->with('status', 'Convite reenviado com sucesso para ' . $user->email);
    }
}
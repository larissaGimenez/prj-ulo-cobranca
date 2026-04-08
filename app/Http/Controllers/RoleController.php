<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Services\RoleService;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RoleController extends Controller
{
    public function __construct(
        protected RoleService $roleService
    ) {
    }

    public function index(): View
    {
        // Eager Loading das permissões para evitar N+1
        $roles = Role::with('permissions')->get();
        return view('roles.index', compact('roles'));
    }

    public function create(): View
    {
        $permissions = Permission::all();
        return view('roles.create', compact('permissions'));
    }

    public function store(RoleRequest $request): RedirectResponse
    {
        $this->roleService->createRole($request->validated());
        return redirect()->route('roles.index')->with('success', 'Perfil criado com sucesso!');
    }

    public function show(Role $role): View
    {
        $role->load('permissions');
        return view('roles.show', compact('role'));
    }

    public function edit(Role $role): View
    {
        $permissions = Permission::all();
        $role->load('permissions');
        return view('roles.edit', compact('role', 'permissions'));
    }

    public function update(RoleRequest $request, Role $role): RedirectResponse
    {
        $this->roleService->updateRole($role, $request->validated());
        return redirect()->route('roles.index')->with('success', 'Perfil atualizado!');
    }
}
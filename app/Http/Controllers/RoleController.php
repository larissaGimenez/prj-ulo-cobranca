<?php

namespace App\Http\Controllers;

use App\Services\RoleService;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
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
        $roles = $this->roleService->getAllRoles();
        return view('roles.index', compact('roles'));
    }

    public function create(): View
    {
        $role = new Role();
        $permissions = Permission::all();
        $groupedByEntity = $permissions->groupBy(function ($perm) {
            $name = trim($perm->name);
            return str_contains($name, '-') ? explode('-', $name, 2)[1] : 'outros';
        });

        return view('roles.create', compact('role', 'groupedByEntity'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'nullable|array'
        ]);

        $this->roleService->createRole($data);

        return redirect()->route('roles.index')
            ->with('status', 'Perfil criado com sucesso!');
    }

    public function show(Role $role): RedirectResponse
    {
        return redirect()->route('roles.edit', $role);
    }

    public function edit(Role $role): View
    {
        $permissions = Permission::all();

        $groupedByEntity = $permissions->groupBy(function ($perm) {
            $name = trim($perm->name);
            return str_contains($name, '-') ? explode('-', $name, 2)[1] : 'outros';
        });

        return view('roles.edit', compact('role', 'groupedByEntity'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array'
        ]);

        $this->roleService->updateRole($role, $data);

        return redirect()->route('roles.index')
            ->with('status', 'Perfil atualizado com sucesso!');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->name === 'admin') {
            return redirect()->route('roles.index')
                ->with('error', 'O perfil administrador é crítico para o sistema e não pode ser removido.');
        }

        $this->roleService->deleteRole($role);

        return redirect()->route('roles.index')
            ->with('status', 'Perfil de acesso removido com sucesso!');
    }
}
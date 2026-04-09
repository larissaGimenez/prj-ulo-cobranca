<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RoleService
{
    public function getAllRoles(): Collection
    {
        // Puxamos com as permissões para evitar o problema de N+1 na listagem
        return Role::with('permissions')->get();
    }

    public function createRole(array $data): Role
    {
        return DB::transaction(function () use ($data) {
            $role = Role::create(['name' => $data['name']]);

            if (isset($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }

            return $role;
        });
    }

    public function updateRole(Role $role, array $data): Role
    {
        return DB::transaction(function () use ($role, $data) {
            $role->update(['name' => $data['name']]);

            // Usamos syncPermissions para garantir que permissões removidas no form
            // também sejam removidas no banco.
            $role->syncPermissions($data['permissions'] ?? []);

            return $role;
        });
    }

    public function deleteRole(Role $role): bool
    {
        // Antes de deletar a Role, o Spatie já remove as relações nas tabelas pivô automaticamente
        return $role->delete();
    }
}
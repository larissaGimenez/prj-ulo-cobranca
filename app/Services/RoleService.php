<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Illuminate\Support\Collection;

class RoleService
{
    public function getAllRoles(): Collection
    {
        return Role::with('permissions')->get();
    }

    public function createRole(array $data): Role
    {
        $role = Role::create(['name' => $data['name']]);

        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return $role;
    }

    public function updateRole(Role $role, array $data): Role
    {
        $role->update(['name' => $data['name']]);
        $role->syncPermissions($data['permissions'] ?? []);

        return $role;
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $modulePermissions = [
            'access.users',
            'access.roles',
            'access.billings',
            'access.sales',
            'access.finances',
            'access.logistics',
            'access.products',
            'access.supports',
            'access.credentials',
        ];

        foreach ($modulePermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);

        // Admin sempre recebe todas as permissões do sistema
        $roleAdmin->syncPermissions(Permission::all());
    }
}
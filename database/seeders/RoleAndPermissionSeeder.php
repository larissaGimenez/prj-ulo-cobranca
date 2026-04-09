<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Limpar cache de permissões
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Criar as Permissões
        $permissions = [
            'visualizar-usuarios',
            'gerenciar-usuarios',
            'visualizar-perfis',
            'gerenciar-perfis',
            'visualizar-cobrancas',
            'criar-cobrancas',
            'cancelar-cobrancas',
            'estornar-pagamentos',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Criar a Role Admin e dar todas as permissões
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleAdmin->syncPermissions(Permission::all());

        // Criar a Role Operador com permissões limitadas
        $roleOperador = Role::firstOrCreate(['name' => 'operador']);
        $roleOperador->syncPermissions([
            'visualizar-cobrancas',
            'criar-cobrancas'
        ]);
    }
}
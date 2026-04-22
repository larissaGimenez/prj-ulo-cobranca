<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            BillingKanbanSeeder::class,
            BillingDemoSeeder::class,
        ]);

        $admin = User::updateOrCreate(
            ['email' => 'admin@material.com.br'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('secret'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $admin->assignRole('admin');

        $this->command->info('Ambiente configurado com sucesso!');
        $this->command->info('Usuário: admin@material.com.br | Senha: secret');
    }
}
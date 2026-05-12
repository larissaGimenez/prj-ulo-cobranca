<?php

namespace App\Services;

use App\Jobs\SendUserInviteJob;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    /**
     * Busca todos os usuários (incluindo deletados) com paginação
     */
    public function getAllPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return User::withTrashed()->latest()->paginate($perPage);
    }

    /**
     * Cria o usuário, atribui Role e dispara convite n8n
     */
    public function create(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'cpf' => $data['cpf'] ?? null,
            'cnpj' => $data['cnpj'] ?? null,
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'] ?? 'user', // Rótulo visual
            'password' => Hash::make(Str::random(32)),
            'status' => 'pending',
        ]);

        // Atribui a Role real no Spatie
        if (isset($data['role'])) {
            $user->assignRole($data['role']);
        }

        $this->sendInvite($user);

        return $user;
    }

    /**
     * Atualiza dados e sincroniza permissões
     */
    public function update(User $user, array $data): bool
    {
        // Tratamento de senha
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Sincroniza Role do Spatie se houver alteração
        if (isset($data['role'])) {
            $user->syncRoles($data['role']);
        }

        return $user->update($data);
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }

    public function restore(User $user): bool
    {
        return $user->restore();
    }

    /**
     * Despacha o convite para a fila (não bloqueia a request HTTP).
     * O invite_sent_at é atualizado de forma síncrona para feedback imediato na UI.
     * A chamada HTTP ao n8n é feita em background pelo SendUserInviteJob.
     */
    public function sendInvite(User $user): void
    {
        $user->update(['invite_sent_at' => now()]);

        Log::info("Convite enfileirado para {$user->email}");

        SendUserInviteJob::dispatch($user);
    }

    public function setUserPassword(User $user, string $password): void
    {
        $user->update([
            'password' => Hash::make($password),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
    }
}
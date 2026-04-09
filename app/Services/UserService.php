<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

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

    /**
     * Lógica de Convite via Webhook n8n
     */
    public function sendInvite(User $user): void
    {
        $setupUrl = URL::temporarySignedRoute(
            'password.setup',
            now()->addHours(2),
            ['user' => $user->id]
        );

        Log::info("Link de convite para {$user->email}: " . $setupUrl);

        $user->update(['invite_sent_at' => now()]);

        Log::info("Tentando enviar Webhook para: " . config('services.n8n.webhook_url'));

        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'X-API-Key' => config('services.n8n.key'),
                ])->post(config('services.n8n.webhook_url'), [
                        'event' => 'user_invite',
                        'user_name' => $user->name,
                        'user_email' => $user->email,
                        'setup_url' => $setupUrl,
                        'expires_at' => now()->addHours(2)->format('d/m/Y H:i'),
                    ]);

            Log::info("Resposta do n8n: " . $response->status());

        } catch (\Exception $e) {
            Log::error("Erro fatal no envio: " . $e->getMessage());
        }
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
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
    public function getAllPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return User::latest()->paginate($perPage);
    }

    public function create(array $data): User
    {
        // 1. Criamos e guardamos na variável $user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make(Str::random(32)),
            'status' => 'pending',
        ]);

        // 2. Agora sim chamamos o convite!
        $this->sendInvite($user);

        // 3. Por último, retornamos o objeto para o Controller
        return $user;
    }

    public function update(User $user, array $data): bool
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        return $user->update($data);
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }

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
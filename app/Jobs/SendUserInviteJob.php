<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class SendUserInviteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Número de tentativas antes de falhar definitivamente.
     */
    public int $tries = 3;

    /**
     * Tempo de espera (segundos) entre tentativas.
     */
    public int $backoff = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected User $user
    ) {}

    /**
     * Envia o convite via Webhook n8n em segundo plano.
     * Anteriormente executado de forma síncrona no UserService::sendInvite(),
     * bloqueando a request HTTP por até 30s em caso de timeout.
     */
    public function handle(): void
    {
        $setupUrl = URL::temporarySignedRoute(
            'password.setup',
            now()->addHours(2),
            ['user' => $this->user->id]
        );

        Log::info("Link de convite para {$this->user->email}: " . $setupUrl);
        Log::info("Tentando enviar Webhook para: " . config('services.n8n.webhook_url'));

        try {
            $response = Http::withoutVerifying()
                ->timeout(15)
                ->withHeaders([
                    'X-API-Key' => config('services.n8n.key'),
                ])->post(config('services.n8n.webhook_url'), [
                    'event' => 'user_invite',
                    'user_name' => $this->user->name,
                    'user_email' => $this->user->email,
                    'setup_url' => $setupUrl,
                    'expires_at' => now()->addHours(2)->format('d/m/Y H:i'),
                ]);

            Log::info("Resposta do n8n: " . $response->status());

        } catch (\Exception $e) {
            Log::error("Erro no envio do convite para {$this->user->email}: " . $e->getMessage());
            throw $e; // Re-throw para que o Laravel faça retry automático
        }
    }
}

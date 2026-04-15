<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantSyncController extends Controller
{
    /**
     * Lista os tenants para o n8n saber quem processar.
     */
    public function index(Request $request)
    {
        $this->authorizeSync($request);

        return Tenant::active()
            ->where('driver', 'omie')
            ->get(['id', 'name', 'app_key', 'app_secret']);
    }

    /**
     * Recebe os títulos atrasados do n8n e salva no banco.
     */
    public function storeTitles(Request $request)
    {
        $this->authorizeSync($request);

        // Aqui você validaria e salvaria os títulos. 
        // Exemplo rápido de lógica:
        // $data = $request->validate([...]);
        // Title::updateOrCreate(['omie_id' => $data['nCodTit']], $data);

        return response()->json(['status' => 'success']);
    }

    private function authorizeSync(Request $request)
    {

        \Log::info('Headers recebidos:', $request->headers->all());

        $clientId = $request->header('X-Client-ID');
        $clientSecret = $request->header('X-Client-Secret');

        // Valida se existe um cliente com estas credenciais e que não foi revogado
        $isAuthorized = \Laravel\Passport\Client::where('id', $clientId)
            ->where('secret', $clientSecret)
            ->where('revoked', false)
            ->exists();

        if (!$isAuthorized) {
            abort(401, 'Credenciais de integração inválidas.');
        }
    }
}
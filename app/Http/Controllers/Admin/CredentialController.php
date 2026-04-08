<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Passport\Client;
use Illuminate\Support\Facades\Hash;

class CredentialController extends Controller
{
    public function index()
    {
        $credentials = Client::where('owner_id', auth()->id())
            ->where('owner_type', auth()->user()->getMorphClass())
            ->get();

        return view('admin.credentials.index', compact('credentials'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:oauth_clients,name'
        ], [
            'name.unique' => 'Já existe uma integração com este nome. Escolha um nome diferente.',
            'name.max' => 'O nome da integração deve ter no máximo 50 caracteres.',
        ]);

        $client = new Client();
        $client->id = (string) \Illuminate\Support\Str::uuid();
        $client->name = $request->name;
        $client->secret = \Illuminate\Support\Str::random(40);
        $client->redirect_uris = '';
        $client->grant_types = 'client_credentials';
        $client->revoked = false;

        $client->owner_id = auth()->id();
        $client->owner_type = auth()->user()->getMorphClass();

        $client->save();

        if ($request->wantsJson()) {
            return response()->json([
                'client_id' => $client->id,
                'client_secret' => $client->secret
            ]);
        }

        return redirect()->back()->with('new_client_secret', $client->secret);
    }

    public function destroy(Request $request, $id)
    {
        $request->validate(['password' => 'required']);

        if (!Hash::check($request->password, auth()->user()->password)) {
            return response()->json(['message' => 'Senha incorreta. Verifique e tente novamente.'], 403);
        }

        Client::where('owner_id', auth()->id())->where('id', $id)->delete();

        return response()->json(['success' => true]);
    }
}
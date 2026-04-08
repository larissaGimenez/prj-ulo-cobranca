<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * @group Testes de Integração
 * * Rota para validar se o Bearer Token está funcionando.
 * @authenticated
 */
Route::middleware('auth:api')->get('/minha-cobranca-teste', function (Request $request) {
    return response()->json([
        'status' => 'sucesso',
        'mensagem' => 'Você acessou uma rota protegida!',
        'usuario' => $request->user()->email,
        'valor' => 150.50 // Exemplo de cobrança 
    ]);
});
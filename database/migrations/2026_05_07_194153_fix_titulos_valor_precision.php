<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Corrigimos o valor dos títulos baseando-se no JSON original (dados_titulo)
        // O valor_documento no JSON Omie já vem no formato numérico correto (ex: 779.33)
        // Isso resolve o bug onde 779.33 foi interpretado como 77933.00 devido a REPLACE('.', '')
        DB::statement("
            UPDATE titulos_conta_receber 
            SET valor = CAST(dados_titulo->>'valor_documento' AS NUMERIC(15,2))
            WHERE dados_titulo->>'valor_documento' IS NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Não há como reverter para o estado quebrado facilmente sem re-aplicar o bug, 
        // então deixamos como está pois o estado atual é o correto.
    }
};

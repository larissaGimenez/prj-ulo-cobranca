<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Converte a coluna data_venc de VARCHAR (DD/MM/YYYY) para DATE nativo do PostgreSQL
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE titulos_conta_receber ALTER COLUMN data_venc TYPE DATE USING to_date(data_venc, 'DD/MM/YYYY')");

        // Converte a coluna valor de VARCHAR (Ex: 1.250,00) para NUMERIC nativo
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE titulos_conta_receber ALTER COLUMN valor TYPE NUMERIC(15,2) USING CAST(REPLACE(REPLACE(valor, '.', ''), ',', '.') AS NUMERIC(15,2))");

        // Recria os índices agora de forma limpa e muito mais rápida
        Schema::table('titulos_conta_receber', function (Blueprint $table) {
            $table->index('data_venc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('titulos_conta_receber', function (Blueprint $table) {
            $table->dropIndex(['data_venc']);
        });

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE titulos_conta_receber ALTER COLUMN data_venc TYPE VARCHAR(255) USING to_char(data_venc, 'DD/MM/YYYY')");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE titulos_conta_receber ALTER COLUMN valor TYPE VARCHAR(255) USING replace(replace(trim(to_char(valor, '999G999G999D99')), ',', ''), '.', ',')");
    }
};

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
        Schema::create('clientes_inadimplentes', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->bigInteger('cliente_id_omie')->nullable()->unique('clientes_inadimplentes_cliente_id_omie_key');
            $table->string('nome', 100)->nullable();
            $table->string('cpf_cnpj', 100)->nullable();
            $table->string('contato', 100)->nullable();
            $table->string('empresa', 100)->nullable();
            $table->jsonb('cliente')->nullable();
            $table->timestamp('data_criacao')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes_inadimplentes');
    }
};

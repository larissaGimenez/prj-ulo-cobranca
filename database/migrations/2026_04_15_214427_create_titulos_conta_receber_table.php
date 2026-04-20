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
        Schema::create('titulos_conta_receber', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->bigInteger('cod_lanc')->nullable()->unique('titulos_conta_receber_cod_lanc_key');
            $table->string('data_venc', 100)->nullable();
            $table->string('numero_parcela', 100)->nullable();
            $table->string('empresa', 100)->nullable();
            $table->jsonb('dados_titulo')->nullable();
            $table->timestamp('data_criacao')->nullable()->useCurrent();
            $table->text('status')->nullable();
            $table->text('valor')->nullable();
            $table->text('cod_cliente')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('titulos_conta_receber');
    }
};

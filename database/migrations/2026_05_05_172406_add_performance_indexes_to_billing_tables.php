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
        Schema::table('titulos_conta_receber', function (Blueprint $table) {
            $table->index('cod_cliente');
        });

        Schema::table('billing_operations', function (Blueprint $table) {
            $table->index('cliente_id_omie');
        });
    }

    public function down(): void
    {
        Schema::table('titulos_conta_receber', function (Blueprint $table) {
            $table->dropIndex(['cod_cliente']);
        });

        Illuminate\Support\Facades\DB::statement('DROP INDEX IF EXISTS idx_titulos_vencimento_date');

        Schema::table('billing_operations', function (Blueprint $table) {
            $table->dropIndex(['cliente_id_omie']);
        });
    }
};

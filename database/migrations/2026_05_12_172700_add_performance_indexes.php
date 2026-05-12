<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Índices de performance identificados no Deep Audit.
     * Usa IF NOT EXISTS para ser idempotente — seguro rodar em ambientes
     * que já possam ter os índices criados manualmente.
     */
    public function up(): void
    {
        // ─── titulos_conta_receber ───
        DB::statement('CREATE INDEX IF NOT EXISTS idx_titulos_cod_cliente  ON titulos_conta_receber (cod_cliente)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_titulos_data_venc    ON titulos_conta_receber (data_venc)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_titulos_status       ON titulos_conta_receber (status)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_titulos_composite    ON titulos_conta_receber (cod_cliente, data_venc, status)');

        // ─── billing_operations ───
        DB::statement('CREATE INDEX IF NOT EXISTS idx_billing_ops_cliente_id_omie ON billing_operations (cliente_id_omie)');

        // ─── clientes_inadimplentes ───
        DB::statement('CREATE INDEX IF NOT EXISTS idx_clientes_data_criacao ON clientes_inadimplentes (data_criacao)');

        // ─── audit_logs ───
        DB::statement('CREATE INDEX IF NOT EXISTS idx_audit_logs_created_at ON audit_logs (created_at)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS idx_titulos_cod_cliente');
        DB::statement('DROP INDEX IF EXISTS idx_titulos_data_venc');
        DB::statement('DROP INDEX IF EXISTS idx_titulos_status');
        DB::statement('DROP INDEX IF EXISTS idx_titulos_composite');
        DB::statement('DROP INDEX IF EXISTS idx_billing_ops_cliente_id_omie');
        DB::statement('DROP INDEX IF EXISTS idx_clientes_data_criacao');
        DB::statement('DROP INDEX IF EXISTS idx_audit_logs_created_at');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Índices de performance identificados no Deep Audit.
     * Tabelas mais impactadas: titulos_conta_receber, billing_operations, clientes_inadimplentes, audit_logs.
     */
    public function up(): void
    {
        // ─── titulos_conta_receber (tabela mais pesada, zero índices existentes) ───
        Schema::table('titulos_conta_receber', function (Blueprint $table) {
            $table->index('cod_cliente', 'idx_titulos_cod_cliente');
            $table->index('data_venc', 'idx_titulos_data_venc');
            $table->index('status', 'idx_titulos_status');

            // Índice composto para as queries de métricas do SyncJob e BillingController::show()
            $table->index(['cod_cliente', 'data_venc', 'status'], 'idx_titulos_composite');
        });

        // ─── billing_operations ───
        Schema::table('billing_operations', function (Blueprint $table) {
            // Usado no updateOrCreate do SyncJob e em BillingController::show()
            $table->index('cliente_id_omie', 'idx_billing_ops_cliente_id_omie');
        });

        // ─── clientes_inadimplentes ───
        Schema::table('clientes_inadimplentes', function (Blueprint $table) {
            // Usado no DashboardController para filtro de semana passada
            $table->index('data_criacao', 'idx_clientes_data_criacao');
        });

        // ─── audit_logs ───
        Schema::table('audit_logs', function (Blueprint $table) {
            // ORDER BY created_at DESC na listagem de logs (AuditLogController::index)
            $table->index('created_at', 'idx_audit_logs_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('titulos_conta_receber', function (Blueprint $table) {
            $table->dropIndex('idx_titulos_cod_cliente');
            $table->dropIndex('idx_titulos_data_venc');
            $table->dropIndex('idx_titulos_status');
            $table->dropIndex('idx_titulos_composite');
        });

        Schema::table('billing_operations', function (Blueprint $table) {
            $table->dropIndex('idx_billing_ops_cliente_id_omie');
        });

        Schema::table('clientes_inadimplentes', function (Blueprint $table) {
            $table->dropIndex('idx_clientes_data_criacao');
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex('idx_audit_logs_created_at');
        });
    }
};

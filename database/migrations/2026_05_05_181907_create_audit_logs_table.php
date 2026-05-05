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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('auditable'); // auditable_id e auditable_type
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('event_type'); // created, updated, deleted, login, etc
            $table->jsonb('payload')->nullable(); // Diferenças (old vs new)
            $table->string('url')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });

        // Índice GIN para buscas ultra-rápidas dentro do JSONB no PostgreSQL
        Illuminate\Support\Facades\DB::statement('CREATE INDEX audit_logs_payload_gin ON audit_logs USING GIN (payload)');
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};

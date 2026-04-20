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
        Schema::create('billing_operations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('cliente_id_omie');
            $table->unsignedBigInteger('billing_kanban_stage_id');
            $table->jsonb('metadata')->nullable();
            $table->timestamps();

            $table->foreign('billing_kanban_stage_id')->references('id')->on('billing_kanban_stages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_operations');
    }
};

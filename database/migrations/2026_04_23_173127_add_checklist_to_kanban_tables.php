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
        Schema::table('billing_kanban_stages', function (Blueprint $table) {
            $table->jsonb('checklist')->nullable()->after('sort_order');
        });

        Schema::table('billing_operations', function (Blueprint $table) {
            $table->jsonb('checklist_data')->nullable()->after('metadata');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billing_kanban_stages', function (Blueprint $table) {
            $table->dropColumn('checklist');
        });

        Schema::table('billing_operations', function (Blueprint $table) {
            $table->dropColumn('checklist_data');
        });
    }
};

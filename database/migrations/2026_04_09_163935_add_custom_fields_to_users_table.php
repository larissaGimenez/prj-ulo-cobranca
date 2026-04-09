<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'cpf')) {
                $table->string('cpf')->nullable()->unique()->after('email');
            }
            if (!Schema::hasColumn('users', 'cnpj')) {
                $table->string('cnpj')->nullable()->unique()->after('cpf');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('cnpj');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('user')->after('phone');
            }
            if (!Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['cpf', 'cnpj', 'phone', 'role']);
            $table->dropSoftDeletes();
        });
    }
};
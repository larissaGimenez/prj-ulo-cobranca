<?php

namespace Tests\Feature;

use App\Jobs\SendUserInviteJob;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class UserCreationTest extends TestCase
{
    use RefreshDatabase;

    private UserService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(UserService::class);

        // Cria roles necessárias para o Spatie (RefreshDatabase limpa entre testes)
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'user',  'guard_name' => 'web']);
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // Intercepta jobs — não faz chamada HTTP real ao n8n
        Queue::fake();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Bug: CNPJ vazio vira string "" em vez de null → UniqueConstraintViolation
    // ─────────────────────────────────────────────────────────────────────────

    public function test_cria_usuario_com_cpf_e_cnpj_vazio_salva_cnpj_como_null(): void
    {
        $user = $this->service->create([
            'name'  => 'Larissa Gimenez',
            'email' => 'larissa@example.com',
            'cpf'   => '99999999999',
            'cnpj'  => '',
            'phone' => '',
            'role'  => 'admin',
        ]);

        $this->assertNull($user->cnpj, 'CNPJ vazio deve ser salvo como NULL');
        $this->assertNull($user->phone, 'Phone vazio deve ser salvo como NULL');
        $this->assertEquals('99999999999', $user->cpf);
    }

    public function test_dois_usuarios_sem_cnpj_nao_geram_unique_violation(): void
    {
        $this->service->create([
            'name'  => 'Usuário Um',
            'email' => 'um@example.com',
            'cpf'   => '11111111111',
            'cnpj'  => '',
            'role'  => 'user',
        ]);

        // Sem a correção, este segundo insert lançaria UniqueConstraintViolationException
        $user2 = $this->service->create([
            'name'  => 'Usuário Dois',
            'email' => 'dois@example.com',
            'cpf'   => '22222222222',
            'cnpj'  => '',
            'role'  => 'user',
        ]);

        $this->assertEquals(2, User::count());
        $this->assertNull($user2->cnpj);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Campos com valor real
    // ─────────────────────────────────────────────────────────────────────────

    public function test_cria_usuario_com_cnpj_salva_valor_correto(): void
    {
        $user = $this->service->create([
            'name'  => 'Empresa Ltda',
            'email' => 'empresa@example.com',
            'cpf'   => '',
            'cnpj'  => '12345678000199',
            'role'  => 'user',
        ]);

        $this->assertEquals('12345678000199', $user->cnpj);
        $this->assertNull($user->cpf);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Status e senha
    // ─────────────────────────────────────────────────────────────────────────

    public function test_usuario_criado_fica_com_status_pending(): void
    {
        $user = $this->service->create([
            'name'  => 'Novo Usuário',
            'email' => 'novo@example.com',
            'role'  => 'user',
        ]);

        $this->assertEquals('pending', $user->status);
    }

    public function test_senha_gerada_automaticamente_e_um_hash_bcrypt(): void
    {
        $user = $this->service->create([
            'name'  => 'Hash Test',
            'email' => 'hash@example.com',
            'role'  => 'user',
        ]);

        $fresh = User::find($user->id);
        $this->assertNotEmpty($fresh->password);
        $this->assertTrue(str_starts_with($fresh->password, '$2y$'), 'Senha deve ser hash bcrypt');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Role (Spatie)
    // ─────────────────────────────────────────────────────────────────────────

    public function test_usuario_recebe_role_correta_ao_ser_criado(): void
    {
        $user = $this->service->create([
            'name'  => 'Admin User',
            'email' => 'admin@example.com',
            'role'  => 'admin',
        ]);

        $this->assertTrue($user->hasRole('admin'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Job de convite
    // ─────────────────────────────────────────────────────────────────────────

    public function test_criacao_despacha_job_de_convite_para_a_fila(): void
    {
        $this->service->create([
            'name'  => 'Job Test',
            'email' => 'job@example.com',
            'role'  => 'user',
        ]);

        Queue::assertPushed(SendUserInviteJob::class);
    }

    public function test_invite_sent_at_e_preenchido_apos_criacao(): void
    {
        $user = $this->service->create([
            'name'  => 'Invite Test',
            'email' => 'invite@example.com',
            'role'  => 'user',
        ]);

        $this->assertNotNull($user->invite_sent_at);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Campos ausentes no array (sem chave alguma)
    // ─────────────────────────────────────────────────────────────────────────

    public function test_campos_opcionais_ausentes_no_array_salvam_como_null(): void
    {
        $user = $this->service->create([
            'name'  => 'Sem Campos',
            'email' => 'semcampos@example.com',
            'role'  => 'user',
            // cpf, cnpj e phone propositalmente ausentes
        ]);

        $this->assertNull($user->cpf);
        $this->assertNull($user->cnpj);
        $this->assertNull($user->phone);
    }
}

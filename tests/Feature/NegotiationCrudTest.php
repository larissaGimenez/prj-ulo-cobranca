<?php

namespace Tests\Feature;

use App\Models\BillingKanbanStage;
use App\Models\BillingOperation;
use App\Models\ClienteInadimplente;
use App\Models\Negotiation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NegotiationCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private BillingOperation $operation;

    protected function setUp(): void
    {
        parent::setUp();

        // Roles mínimas para autenticação funcionar com Spatie
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'user',  'guard_name' => 'web']);
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $this->admin = User::factory()->create(['status' => 'active', 'role' => 'admin']);
        $this->admin->assignRole('admin');

        // Etapa do Kanban necessária para criar BillingOperation
        $stage = BillingKanbanStage::create(['name' => 'Inicial', 'sort_order' => 0, 'checklist' => []]);

        // ClienteInadimplente necessário para a view edit.blade.php (linha: $operation->cliente->nome)
        // O model tem $incrementing=false — ID vem do Omie, deve ser fornecido explicitamente
        ClienteInadimplente::create([
            'id'              => 1,
            'cliente_id_omie' => 99999,
            'nome'            => 'Cliente Teste',
            'empresa'         => 'Empresa Teste',
            'data_criacao'    => now(),
        ]);

        // Operação base que será vinculada às negociações
        $this->operation = BillingOperation::create([
            'cliente_id_omie'         => 99999,
            'billing_kanban_stage_id' => $stage->id,
            'metadata'                => ['cliente' => ['nome' => 'Cliente Teste', 'empresa' => 'Empresa Teste']],
            'checklist_data'          => [],
        ]);

        // Desabilita verificação de CSRF — o APP_ENV=testing do phpunit.xml não está sendo aplicado
        // porque o .env local sobrescreve as variáveis de ambiente
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Guard: rotas protegidas por autenticação
    // ─────────────────────────────────────────────────────────────────────────

    public function test_guest_e_redirecionado_para_login_ao_acessar_index(): void
    {
        $this->get(route('negotiations.index'))->assertRedirect(route('login'));
    }

    public function test_guest_e_redirecionado_para_login_ao_acessar_create(): void
    {
        $this->get(route('negotiations.create'))->assertRedirect(route('login'));
    }

    public function test_guest_nao_pode_criar_negociacao(): void
    {
        $this->post(route('negotiations.store'), [])->assertRedirect(route('login'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────────────────────────────────

    public function test_usuario_autenticado_acessa_listagem_de_negociacoes(): void
    {
        $this->actingAs($this->admin)
            ->get(route('negotiations.index'))
            ->assertOk()
            ->assertViewIs('negotiations.index');
    }

    public function test_negociacoes_aparecem_na_listagem(): void
    {
        $this->createNegotiation();

        $this->actingAs($this->admin)
            ->get(route('negotiations.index'))
            ->assertOk()
            ->assertViewHas('negotiations');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CREATE / STORE
    // ─────────────────────────────────────────────────────────────────────────

    public function test_formulario_de_criacao_renderiza_corretamente(): void
    {
        $this->actingAs($this->admin)
            ->get(route('negotiations.create'))
            ->assertOk()
            ->assertViewIs('negotiations.create')
            ->assertViewHas('operations');
    }

    public function test_store_cria_negociacao_e_redireciona_para_index(): void
    {
        $this->actingAs($this->admin)
            ->post(route('negotiations.store'), $this->validPayload())
            ->assertRedirect(route('negotiations.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('negotiations', [
            'operation_id' => $this->operation->id,
            'status'       => 'em andamento',
        ]);
    }

    public function test_store_salva_details_corretamente(): void
    {
        $this->actingAs($this->admin)
            ->post(route('negotiations.store'), $this->validPayload());

        $negotiation = Negotiation::first();
        $this->assertNotNull($negotiation);
        $this->assertEquals(1500.00, (float) $negotiation->details['valor_proposta']);
        $this->assertEquals('Parcelado', $negotiation->details['tipo_acordo']);
    }

    public function test_store_limpa_formato_moeda_brasileiro(): void
    {
        $payload = $this->validPayload([
            'details' => [
                'valor_proposta'  => '1.500,00',
                'valor_entrada'   => '500,00',
                'numero_parcelas' => 2,
                'valor_parcela'   => '500,00',
                'tipo_acordo'     => 'Parcelado',
                'data_vencimento' => '2026-12-01',
            ],
        ]);

        $this->actingAs($this->admin)->post(route('negotiations.store'), $payload);

        $negotiation = Negotiation::first();
        $this->assertEquals(1500.00, (float) $negotiation->details['valor_proposta']);
        $this->assertEquals(500.00,  (float) $negotiation->details['valor_parcela']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Validação do STORE
    // ─────────────────────────────────────────────────────────────────────────

    public function test_store_falha_sem_operation_id(): void
    {
        $this->actingAs($this->admin)
            ->post(route('negotiations.store'), $this->validPayload(['operation_id' => null]))
            ->assertSessionHasErrors('operation_id');
    }

    public function test_store_falha_com_operation_id_inexistente(): void
    {
        $this->actingAs($this->admin)
            ->post(route('negotiations.store'), $this->validPayload(['operation_id' => 99999999]))
            ->assertSessionHasErrors('operation_id');
    }

    public function test_store_falha_com_status_invalido(): void
    {
        $this->actingAs($this->admin)
            ->post(route('negotiations.store'), $this->validPayload(['status' => 'invalido']))
            ->assertSessionHasErrors('status');
    }

    public function test_store_falha_sem_valor_proposta(): void
    {
        $payload = $this->validPayload();
        unset($payload['details']['valor_proposta']);

        $this->actingAs($this->admin)
            ->post(route('negotiations.store'), $payload)
            ->assertSessionHasErrors('details.valor_proposta');
    }

    public function test_store_falha_com_tipo_acordo_invalido(): void
    {
        $payload = $this->validPayload();
        $payload['details']['tipo_acordo'] = 'Inválido';

        $this->actingAs($this->admin)
            ->post(route('negotiations.store'), $payload)
            ->assertSessionHasErrors('details.tipo_acordo');
    }

    public function test_todos_os_status_validos_sao_aceitos(): void
    {
        foreach (['em andamento', 'quitado', 'cancelado'] as $status) {
            $this->actingAs($this->admin)
                ->post(route('negotiations.store'), $this->validPayload(['status' => $status]))
                ->assertRedirect(route('negotiations.index'));
        }

        $this->assertEquals(3, Negotiation::count());
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────────────────────────────────

    public function test_show_exibe_negociacao_existente(): void
    {
        $negotiation = $this->createNegotiation();

        $this->actingAs($this->admin)
            ->get(route('negotiations.show', $negotiation))
            ->assertOk()
            ->assertViewIs('negotiations.show')
            ->assertViewHas('negotiation', fn($n) => $n->id === $negotiation->id);
    }

    public function test_show_retorna_404_para_negociacao_inexistente(): void
    {
        $this->actingAs($this->admin)
            ->get(route('negotiations.show', 999999))
            ->assertNotFound();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // EDIT / UPDATE
    // ─────────────────────────────────────────────────────────────────────────

    public function test_formulario_de_edicao_renderiza_com_negociacao(): void
    {
        $negotiation = $this->createNegotiation();

        $this->actingAs($this->admin)
            ->get(route('negotiations.edit', $negotiation))
            ->assertOk()
            ->assertViewIs('negotiations.edit')
            ->assertViewHas('negotiation', fn($n) => $n->id === $negotiation->id)
            ->assertViewHas('operations');
    }

    public function test_update_altera_dados_e_redireciona_para_index(): void
    {
        $negotiation = $this->createNegotiation();

        $this->actingAs($this->admin)
            ->put(route('negotiations.update', $negotiation), $this->validPayload(['status' => 'quitado']))
            ->assertRedirect(route('negotiations.index'))
            ->assertSessionHas('success');

        $this->assertEquals('quitado', $negotiation->fresh()->status);
    }

    public function test_update_falha_com_status_invalido(): void
    {
        $negotiation = $this->createNegotiation();

        $this->actingAs($this->admin)
            ->put(route('negotiations.update', $negotiation), $this->validPayload(['status' => 'invalido']))
            ->assertSessionHasErrors('status');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────────────────────────────────

    public function test_destroy_remove_negociacao_do_banco(): void
    {
        $negotiation = $this->createNegotiation();

        $this->actingAs($this->admin)
            ->delete(route('negotiations.destroy', $negotiation))
            ->assertRedirect(route('negotiations.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('negotiations', ['id' => $negotiation->id]);
    }

    public function test_destroy_retorna_404_para_negociacao_inexistente(): void
    {
        $this->actingAs($this->admin)
            ->delete(route('negotiations.destroy', 999999))
            ->assertNotFound();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'operation_id' => $this->operation->id,
            'status'       => 'em andamento',
            'details'      => [
                'valor_proposta'  => 1500.00,
                'valor_entrada'   => 300.00,
                'numero_parcelas' => 3,
                'valor_parcela'   => 400.00,
                'tipo_acordo'     => 'Parcelado',
                'data_vencimento' => '2026-12-01',
                'observacoes'     => 'Teste de negociação',
            ],
        ], $overrides);
    }

    private function createNegotiation(array $attrs = []): Negotiation
    {
        return Negotiation::create(array_merge([
            'operation_id' => $this->operation->id,
            'status'       => 'em andamento',
            'details'      => [
                'valor_proposta'  => 1000.00,
                'numero_parcelas' => 1,
                'tipo_acordo'     => 'À vista',
            ],
        ], $attrs));
    }
}

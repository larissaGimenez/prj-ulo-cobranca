<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClienteInadimplente extends Model
{
    // Define o nome exato da tabela no banco
    protected $table = 'clientes_inadimplentes';

    // Como o ID é preenchido manualmente (não é auto-incremento)
    public $incrementing = false;
    protected $keyType = 'int';

    // Desativa timestamps padrão (created_at/updated_at) 
    // já que a migration usa 'data_criacao'
    public $timestamps = false;

    protected $fillable = [
        'id',
        'cliente_id_omie',
        'nome',
        'cpf_cnpj',
        'contato',
        'empresa',
        'cliente',
        'data_criacao'
    ];

    protected $casts = [
        'cliente' => 'array', // Converte o JSONB automaticamente para array
        'data_criacao' => 'datetime'
    ];

    /**
     * Relacionamento: Um cliente possui muitos títulos a receber.
     * Chave estrangeira: 'cod_cliente' na tabela de títulos.
     * Chave local: 'cliente_id_omie' nesta tabela.
     */
    public function titulos(): HasMany
    {
        // Adicionamos um select com cast para evitar o erro de bigint = text do Postgres
        return $this->hasMany(TituloContaReceber::class, 'cod_cliente', 'cliente_id_omie');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TituloContaReceber extends Model
{
    protected $table = 'titulos_conta_receber';

    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'cod_lanc',
        'data_venc',
        'numero_parcela',
        'empresa',
        'dados_titulo',
        'data_criacao',
        'status',
        'valor',
        'cod_cliente'
    ];

    protected $casts = [
        'dados_titulo' => 'array',
        'data_criacao' => 'datetime',
        'cod_cliente' => 'string',
    ];

    /**
     * Relacionamento: O título pertence a um cliente inadimplente.
     */
    public function clienteDevedor(): BelongsTo
    {
        return $this->belongsTo(ClienteInadimplente::class, 'cod_cliente', 'cliente_id_omie');
    }

    public function getValorFloatAttribute()
    {
        $valorRaw = trim($this->valor);

        if (str_contains($valorRaw, ',') && str_contains($valorRaw, '.')) {
            // Ex: 1.250,00 -> 1250.00
            $valorLimpo = str_replace(['.', ','], ['', '.'], $valorRaw);
        } elseif (str_contains($valorRaw, ',')) {
            // Ex: 359,2 -> 359.2
            $valorLimpo = str_replace(',', '.', $valorRaw);
        } else {
            // Ex: 359.2 -> 359.2
            $valorLimpo = $valorRaw;
        }

        return (float) $valorLimpo;
    }
}
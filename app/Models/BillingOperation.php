<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Negotiation;

class BillingOperation extends Model
{
    use HasFactory;

    protected $table = 'billing_operations';

    protected $fillable = [
        'cliente_id_omie',
        'billing_kanban_stage_id',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Uma operação pertence a uma etapa.
     */
    public function stage(): BelongsTo
    {
        return $this->belongsTo(BillingKanbanStage::class, 'billing_kanban_stage_id');
    }

    /**
     * Tenta buscar o cliente atual associado (opcional, já que temos snapshot).
     */
    public function cliente()
    {
        return $this->belongsTo(ClienteInadimplente::class, 'cliente_id_omie', 'cliente_id_omie');
    }

    public function negotiations()
    {
        return $this->hasMany(Negotiation::class, 'operation_id');
    }
}

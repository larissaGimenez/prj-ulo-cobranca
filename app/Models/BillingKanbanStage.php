<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Auditable;

class BillingKanbanStage extends Model
{
    use HasFactory, Auditable;

    protected $table = 'billing_kanban_stages';

    protected $fillable = [
        'name',
        'sort_order',
        'checklist'
    ];

    protected $casts = [
        'checklist' => 'array'
    ];

    /**
     * Uma etapa possui várias operações (cards).
     */
    public function operations(): HasMany
    {
        return $this->hasMany(BillingOperation::class, 'billing_kanban_stage_id');
    }
}

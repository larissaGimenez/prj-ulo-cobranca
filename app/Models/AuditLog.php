<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = [
        'auditable_id',
        'auditable_type',
        'user_id',
        'event_type',
        'payload',
        'url',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'payload' => 'array'
    ];

    /**
     * Relacionamento polimórfico (O modelo que foi auditado)
     */
    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Usuário que realizou a ação
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

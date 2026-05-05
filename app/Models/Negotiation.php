<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;

use App\Traits\Auditable;

class Negotiation extends Model
{
    use Auditable;
    protected $fillable = ['operation_id', 'status', 'details'];

    protected $casts = [
        'details' => AsArrayObject::class,
    ];

    public function operation()
    {
        return $this->belongsTo(BillingOperation::class, 'operation_id');
    }
}

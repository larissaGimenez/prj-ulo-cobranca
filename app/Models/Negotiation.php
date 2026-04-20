<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;

class Negotiation extends Model
{
    protected $fillable = ['operation_id', 'status', 'details'];

    protected $casts = [
        'details' => AsArrayObject::class,
    ];
}

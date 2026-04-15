<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'driver',
        'app_key',
        'app_secret',
        'settings',
        'is_active',
    ];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'app_secret' => 'encrypted',
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    protected $attributes = [
        'driver' => 'omie',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;

#[Fillable(['name', 'email', 'password', 'status', 'invite_sent_at', 'cpf', 'cnpj', 'phone', 'role', 'deleted_at'])]
#[Hidden(['password', 'remember_token'])]


class User extends Authenticatable
{

    use HasFactory, Notifiable, HasApiTokens, SoftDeletes, HasRoles;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'invite_sent_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isInviteValid(): bool
    {
        if (!$this->invite_sent_at || $this->status !== 'pending') {
            return false;
        }

        return $this->invite_sent_at->addHours(2)->isFuture();
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && !$this->trashed();
    }

    /**
     * Mutator para o CPF: Remove máscara ao salvar.
     */
    protected function cpf(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value, // Opcional: Você poderia formatar aqui na saída
            set: fn($value) => preg_replace('/[^0-9]/', '', $value),
        );
    }

    /**
     * Mutator para o CNPJ: Remove máscara ao salvar.
     */
    protected function cnpj(): Attribute
    {
        return Attribute::make(
            set: fn($value) => preg_replace('/[^0-9]/', '', $value),
        );
    }

    /**
     * Mutator para o Telefone: Remove parênteses, espaços e hífens.
     */
    protected function phone(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$value)
                    return null;
                return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $value);
            },
            set: fn($value) => preg_replace('/[^0-9]/', '', $value),
        );
    }
}
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

#[Fillable(['name', 'email', 'password', 'status', 'invite_sent_at'])]
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
}
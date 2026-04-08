<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    // 1. Autoriza a requisição (qualquer um pode se registrar)
    public function authorize(): bool
    {
        return true;
    }

    // 2. Define as regras de validação (copiadas do Controller original do Breeze)
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }
}
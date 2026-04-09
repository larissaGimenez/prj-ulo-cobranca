<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Captura o ID do usuário da rota (funciona se a rota for /users/{user})
        $userId = $this->route('user')?->id ?? $this->user;

        return [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId),
            ],

            'cpf' => [
                'required_without:cnpj',
                'nullable',
                'string',
                'max:14',
                Rule::unique('users')->ignore($userId)
            ],

            'cnpj' => [
                'required_without:cpf',
                'nullable',
                'string',
                'max:18',
                Rule::unique('users')->ignore($userId)
            ],

            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'string'],
            'status' => ['required', 'string', 'in:active,pending,inactive'],

            'password' => [
                'nullable',
                'confirmed',
                Password::defaults()
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'cpf.required_without' => 'O campo CPF é obrigatório quando não há um CNPJ.',
            'cnpj.required_without' => 'O campo CNPJ é obrigatório quando não há um CPF.',
            'cpf.unique' => 'Este CPF já pertence a outro usuário.',
            'cnpj.unique' => 'Este CNPJ já pertence a outro usuário.',
            'email.unique' => 'Este e-mail já está sendo utilizado.',
            'role.required' => 'O campo função é obrigatório.',
            'status.required' => 'O campo status é obrigatório.',
            'status.in' => 'O campo status deve ser ativo, pendente ou inativo.',
            'password.confirmed' => 'As senhas não conferem.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'password.max' => 'A senha deve ter no máximo 12 caracteres.',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'cpf' => $this->cpf ? preg_replace('/\D/', '', $this->cpf) : null,
            'cnpj' => $this->cnpj ? preg_replace('/\D/', '', $this->cnpj) : null,
            'phone' => $this->phone ? preg_replace('/\D/', '', $this->phone) : null,
        ]);
    }
}
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'cpf' => ['required_without:cnpj', 'nullable', 'string', 'max:14', 'unique:users,cpf'],
            'cnpj' => ['required_without:cpf', 'nullable', 'string', 'max:18', 'unique:users,cnpj'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'status' => ['nullable', 'string', 'in:active,pending,inactive'],
        ];
    }
    public function messages(): array
    {
        return [
            'cpf.required_without' => 'O campo CPF é obrigatório quando não há um CNPJ.',
            'cnpj.required_without' => 'O campo CNPJ é obrigatório quando não há um CPF.',
            'cpf.unique' => 'Este CPF já está cadastrado em nossa base.',
            'cnpj.unique' => 'Este CNPJ já está cadastrado em nossa base.',
            'email.unique' => 'Este e-mail já está sendo utilizado.',
            'role.required' => 'O campo função é obrigatório.',
            'status.required' => 'O campo status é obrigatório.',
            'status.in' => 'O campo status deve ser ativo, pendente ou inativo.',
            'password.required' => 'O campo senha é obrigatório.',
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
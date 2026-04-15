<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): bool|array
    {
        $tenantId = $this->route('tenant');

        return [
            'name' => ['required', 'string', 'max:255'],

            'driver' => ['required', 'string', Rule::in(['omie'])],

            'app_key' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tenants', 'app_key')->ignore($tenantId),
            ],

            'app_secret' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'string',
            ],

            'is_active' => ['nullable', 'boolean'],

            'settings' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do aplicativo é obrigatório.',
            'app_key.required' => 'A App Key é indispensável para a integração.',
            'app_key.unique' => 'Esta App Key já está cadastrada em outro tenant.',
            'app_secret.required' => 'O App Secret deve ser informado no cadastro.',
            'driver.in' => 'Por enquanto, aceitamos apenas o sistema Omie.',
        ];
    }
}
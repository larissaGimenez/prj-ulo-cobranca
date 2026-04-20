<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorenegotiationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'operation_id' => 'required|exists:billing_operations,id',
            'status' => 'required|string|in:em aberto,em andamento,concluído,cancelado',
            'details' => [
                'valor_proposta' => 'required|numeric',
                'numero_parcelas' => 'required|integer',
                'valor_parcela' => 'required|numeric',
                'data_primeiro_vencimento' => 'required|date'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'operation_id.required' => 'Operação é obrigatória.',
            'operation_id.exists' => 'Operação inválida.',
            'status.required' => 'Status é obrigatório.',
            'status.in' => 'Status inválido.',
            'details.valor_proposta.required' => 'Valor da proposta é obrigatório.',
            'details.valor_proposta.numeric' => 'Valor da proposta deve ser numérico.',
            'details.numero_parcelas.required' => 'Número de parcelas é obrigatório.',
            'details.numero_parcelas.integer' => 'Número de parcelas deve ser inteiro.',
            'details.valor_parcela.required' => 'Valor da parcela é obrigatório.',
            'details.valor_parcela.numeric' => 'Valor da parcela deve ser numérico.',
            'details.data_primeiro_vencimento.required' => 'Data do primeiro vencimento é obrigatória.',
            'details.data_primeiro_vencimento.date' => 'Data do primeiro vencimento inválida.'
        ];
    }
}

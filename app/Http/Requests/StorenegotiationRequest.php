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
            'status' => 'required|string|in:em andamento,quitado,cancelado',
            'details' => 'required|array',
            'details.valor_proposta' => 'required|numeric',
            'details.valor_entrada' => 'nullable|numeric',
            'details.numero_parcelas' => 'required|integer',
            'details.valor_parcela' => 'nullable|numeric',
            'details.data_vencimento' => 'nullable|date',
            'details.tipo_acordo' => 'required|string|in:À vista,Parcelado,Prorrogação',
            'details.observacoes' => 'nullable|string',
            'details.parcelas' => 'nullable|array',
            'details.parcelas.*.valor' => 'nullable|numeric',
            'details.parcelas.*.vencimento' => 'nullable|date',
            'details.parcelas.*.id' => 'nullable|integer',
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
            'details.data_vencimento.required' => 'Data de vencimento é obrigatória.',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('details')) {
            $details = $this->input('details');

            // Limpar valores principais
            if (isset($details['valor_proposta'])) {
                $details['valor_proposta'] = $this->cleanMoney($details['valor_proposta']);
            }
            if (isset($details['valor_entrada'])) {
                $details['valor_entrada'] = $this->cleanMoney($details['valor_entrada']);
            }
            if (isset($details['valor_parcela'])) {
                $details['valor_parcela'] = $this->cleanMoney($details['valor_parcela']);
            }

            // Limpar valores das parcelas
            if (isset($details['parcelas']) && is_array($details['parcelas'])) {
                foreach ($details['parcelas'] as $key => $parcela) {
                    if (isset($parcela['valor'])) {
                        $details['parcelas'][$key]['valor'] = $this->cleanMoney($parcela['valor']);
                    }
                }
            }

            $this->merge(['details' => $details]);
        }
    }

    private function cleanMoney($value)
    {
        if (is_null($value) || $value === '') return null;
        // Transforma "999.999,99" em "999999.99"
        return str_replace(',', '.', str_replace('.', '', $value));
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReservaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Qualquer utilizador pode reservar
    }

    public function rules(): array
    {
        return [
            'tipo_ingresso_id' => ['required', 'integer', 'exists:tipo_ingressos,id'],
            'nome_cliente'     => ['required', 'string', 'max:150'],
            'whatsapp'         => ['required', 'string', 'max:20', 'regex:/^\+?[0-9\s\-]{9,20}$/'],
            'quantidade'       => ['required', 'integer', 'min:1', 'max:10'],
            'comprovativo'     => ['required', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'tipo_ingresso_id.exists' => 'Tipo de bilhete inválido.',
            'whatsapp.regex'          => 'Número de WhatsApp inválido.',
            'comprovativo.required'   => 'O comprovativo de pagamento é obrigatório.',
            'comprovativo.mimes'      => 'O comprovativo deve ser JPG, PNG ou PDF.',
            'comprovativo.max'        => 'O comprovativo não pode ter mais de 5MB.',
        ];
    }
}
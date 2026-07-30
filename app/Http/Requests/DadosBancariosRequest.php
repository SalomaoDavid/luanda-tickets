<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DadosBancariosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() &&
               in_array(auth()->user()->role, ['creator', 'admin']);
    }

    public function rules(): array
    {
        return [
            'nome_banco'   => ['required', 'string', 'max:100'],
            'titular'      => ['required', 'string', 'max:150'],
            'iban'         => ['required', 'string', 'max:50', 'regex:/^[A-Z]{2}[0-9A-Z\s\.]+$/'],
            'numero_conta' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'iban.regex' => 'O IBAN tem um formato inválido.',
        ];
    }
}
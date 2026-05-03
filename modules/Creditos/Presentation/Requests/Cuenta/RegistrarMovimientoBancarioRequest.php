<?php

namespace Modules\Creditos\Presentation\Requests\Cuenta;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegistrarMovimientoBancarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo' => ['required', 'string', Rule::in(['RETIRAR', 'ABONAR', 'TRANSFERIR'])],
            'agencia_operacion' => ['required', 'integer', 'min:1'],
            'agencia_banco' => ['required', 'integer', 'min:1', 'same:agencia_operacion'],
            'banco_id' => ['required', 'integer', 'min:1'],
            'caja_operacion' => ['nullable', 'integer', 'min:1'],
            'cuenta_operacion' => ['nullable', 'integer', 'min:1'],
            'frmDatosMovimiento' => ['required', 'string', 'json'],
        ];
    }

    public function messages(): array
    {
        return [
            'agencia_banco.same' => 'Para garantizar consistencia, la agencia del banco debe coincidir con la agencia de la operación.',
            'frmDatosMovimiento.json' => 'El detalle del movimiento es inválido.',
        ];
    }
}


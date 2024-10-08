<?php

namespace App\Http\Requests\OrderPicking;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class OrderPickingCancelRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Error de validación.',
            'errors' => $validator->errors()
        ], 422));
    }

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => ['required', 'exists:order_pickings,id'],
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'El Identificador de la orden de alistamiento es requerido.',
            'id.exists' => 'El Identificador de la orden de alistamiento no es válido.'
        ];
    }
}

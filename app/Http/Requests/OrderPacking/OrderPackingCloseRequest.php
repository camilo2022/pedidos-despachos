<?php

namespace App\Http\Requests\OrderPacking;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class OrderPackingCloseRequest extends FormRequest
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
            'order_package_id' => ['required', 'exists:order_packages,id'],
            'weight' => ['required', 'string']
        ];
    }

    public function messages()
    {
        return [
            'order_package_id.required' => 'El Identificador del empaque de la orden de empacado es requerido.',
            'order_package_id.exists' => 'El Identificador del empaque de la orden de empacado no es válido.',
            'weight.required' => 'El Peso del empaque de la orden de empacado es requerido.',
            'weight.string' => 'El Peso del empaque de la orden de empacado debe ser una cadena de caracteres.'
        ];
    }
}

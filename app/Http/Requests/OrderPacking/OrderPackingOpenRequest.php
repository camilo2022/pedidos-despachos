<?php

namespace App\Http\Requests\OrderPacking;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class OrderPackingOpenRequest extends FormRequest
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
            'order_package_id' => ['required', 'exists:order_packages,id']
        ];
    }

    public function messages()
    {
        return [
            'order_package_id.required' => 'El Identificador del empaque de la orden de empacado es requerido.',
            'order_package_id.exists' => 'El Identificador del empaque de la orden de empacado no es válido.'
        ];
    }
}

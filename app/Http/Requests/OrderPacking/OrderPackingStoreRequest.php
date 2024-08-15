<?php

namespace App\Http\Requests\OrderPacking;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class OrderPackingStoreRequest extends FormRequest
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
            'id' => ['required', 'exists:order_packings,id'],
            'package_type_id' => ['required', 'exists:package_types,id'],
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'El Identificador de la orden de empacado es requerido.',
            'id.exists' => 'El Identificador de la orden de empacado no es válido.',
            'package_type_id.required' => 'El Identificador del tipo de empaque es requerido.',
            'package_type_id.exists' => 'El Identificador del tipo de empaque no es válido.'
        ];
    }
}

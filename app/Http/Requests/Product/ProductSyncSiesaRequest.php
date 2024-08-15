<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductSyncSiesaRequest extends FormRequest
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
            'referencia' => ['required', 'string']
        ];
    }

    public function messages()
    {
        return [
            'referencia.required' => 'El campo Referencia es requerido.',
            'referencia.string' => 'El campo Referencia debe ser una cadena de texto.'
        ];
    }
}

<?php

namespace App\Http\Requests\Filter;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class FilterQueryRequest extends FormRequest
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
            'product_id' => ['required', 'exists:products,id'],
            'color_id' => ['required', 'exists:colors,id']
        ];
    }

    public function messages()
    {
        return [
            'product_id.required' => 'El Identificador del producto es requerido.',
            'product_id.exists' => 'El Identificador del producto no es válido.',
            'color_id.required' => 'El Identificador del color es requerido.',
            'color_id.exists' => 'El Identificador del color no es válido.'
        ];
    }
}

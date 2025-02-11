<?php

namespace App\Http\Requests\POS;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PersonPOSRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Error de validación.',
            'errors' => $validator->errors()
        ], 422));
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'search' => $this->input('search') == 'true' ? true : false,
        ]);
    }

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'search' => ['required', 'boolean'],
            'value' => ['required', 'string', 'min:1']
        ];
    }

    public function messages()
    {
        return [
            'search.required' => 'El campo Buscar por Documento es requerido.',
            'search.boolean' => 'El campo Buscar por Documento debe ser true o false.',
            'value.required' => 'El campo Valor es requerido.',
            'value.string' => 'El campo Valor debe ser una cadena de caracteres.',
            'value.min' => 'El campo Valor debe tener minimo 1 caracteres.',
        ];
    }
}

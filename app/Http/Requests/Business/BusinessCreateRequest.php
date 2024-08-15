<?php

namespace App\Http\Requests\Business;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BusinessCreateRequest extends FormRequest
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
            'country' => ['nullable', 'exists:countries,name'],
            'departament' => ['nullable', 'exists:departaments,name']
        ];
    }

    public function messages()
    {
        return [
            'country.exists' => 'El pais no es valido.',
            'departament.exists' => 'El departamento no es valido.'
        ];
    }
}

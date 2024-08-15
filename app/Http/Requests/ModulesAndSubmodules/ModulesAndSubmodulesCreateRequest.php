<?php

namespace App\Http\Requests\ModulesAndSubmodules;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ModulesAndSubmodulesCreateRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Error de validación',
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
            'role' => ['nullable', 'string', 'exists:roles,name'],
        ];
    }

    public function messages()
    {
        return [
            'role.*.string' => 'Cada elemento en el campo Roles debe ser una cadena de caracteres.',
            'role.*.exists' => 'El Rol no es valido.'
        ];
    }
}

<?php

namespace App\Http\Requests\ModulesAndSubmodules;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ModulesAndSubmodulesDeleteRequest extends FormRequest
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
            'id' => ['required', 'exists:modules,id'],
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'El Identificador del modulo es requerido.',
            'id.exists' => 'El Identificador del modulo no es valido.',
        ];
    }
}

<?php

namespace App\Http\Requests\Correria;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CorreriaDeleteRequest extends FormRequest
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
            'id' => ['required', 'exists:correrias,id'],
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'El Identificador de la correria es requerido.',
            'id.exists' => 'El Identificador de la correria no es válido.',
        ];
    }
}

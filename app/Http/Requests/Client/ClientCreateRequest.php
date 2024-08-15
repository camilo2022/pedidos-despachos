<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ClientCreateRequest extends FormRequest
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
            'country_id' => ['nullable', 'exists:countries,id'],
            'departament_id' => ['nullable', 'exists:departaments,id'],
            'person_type_id' => ['nullable', 'exists:person_types,id'],
        ];
    }

    public function messages()
    {
        return [
            'country_id.exists' => 'El Identificador del pais no es valido.',
            'departament_id.exists' => 'El Identificador del departamento no es valido.',
            'person_type_id.exists' => 'El identificador del tipo de persona no es valido.',
        ];
    }
}

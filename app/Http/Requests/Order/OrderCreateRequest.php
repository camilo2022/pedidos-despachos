<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class OrderCreateRequest extends FormRequest
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
            'client_id' => ['nullable', 'exists:clients,id']
        ];
    }

    public function messages()
    {
        return [
            'client_id.required' => 'El Identificador del cliente es requerido.',
            'client_id.exists' => 'El Identificador del cliente no es valido.'
        ];
    }
}

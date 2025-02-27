<?php

namespace App\Http\Requests\Libranza;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LibranzaCancelRequest extends FormRequest
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
            'id' => ['required', 'exists:libranzas,id']
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'El Identificador de la libranza es requerido.',
            'id.exists' => 'El Identificador de la libranza no es valido.'
        ];
    }
}

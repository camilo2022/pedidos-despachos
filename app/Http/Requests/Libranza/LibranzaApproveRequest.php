<?php

namespace App\Http\Requests\Libranza;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class LibranzaApproveRequest extends FormRequest
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
            'id' => ['required', 'exists:libranzas,id'],
            'share' => ['required', Rule::in([1, 2, 3, 4])],
            'code' => ['required', 'exists:libranzas,code,' . $this->route('id')]
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'El Identificador de la libranza es requerido.',
            'id.exists' => 'El Identificador de la libranza no es valido.',
            'share.required' => 'El campo Numero de cuotas es requerido.',
            'share.in' => 'El campo campo Numero de cuotas es invalido.',
            'code.required' => 'El campo Codigo de confirmacion de la libranza es requerido.',
            'code.exists' => 'El  Codigo de confirmacion de la libranza no es valido.',
        ];
    }
}

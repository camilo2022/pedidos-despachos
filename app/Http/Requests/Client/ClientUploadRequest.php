<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ClientUploadRequest extends FormRequest
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
            'wallets' => ['required', 'file', 'mimes:csv,xls,xlsx'],
        ];
    }

    public function messages()
    {
        return [
            'wallets.required' => 'El campo Archivo de carteras es requerido.',
            'wallets.file' => 'El campo Archivo de carteras debe ser un archivo.',
            'wallets.mimes' => 'El Archivo de carteras debe tener una extensión válida (csv, xls, xlsx).',
        ];
    }
}

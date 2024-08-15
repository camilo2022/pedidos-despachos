<?php

namespace App\Http\Requests\Filter;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class FilterUploadRequest extends FormRequest
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
            'cuts' => ['required', 'file', 'mimes:csv,xls,xlsx'],
        ];
    }

    public function messages()
    {
        return [
            'cuts.required' => 'El campo Archivo de cortes es requerido.',
            'cuts.file' => 'El campo Archivo de cortes debe ser un archivo.',
            'cuts.mimes' => 'El Archivo de cortes debe tener una extensión válida (csv, xls, xlsx).',
        ];
    }
}

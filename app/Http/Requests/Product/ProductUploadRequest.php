<?php

namespace App\Http\Requests\Product;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductUploadRequest extends FormRequest
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
            'products' => ['required', 'file', 'mimes:csv,xls,xlsx'],
        ];
    }

    public function messages()
    {
        return [
            'products.required' => 'El campo Archivo de productos es requerido.',
            'products.file' => 'El campo Archivo de productos debe ser un archivo.',
            'products.mimes' => 'El Archivo de productos debe tener una extensión válida (csv, xls, xlsx).',
        ];
    }
}

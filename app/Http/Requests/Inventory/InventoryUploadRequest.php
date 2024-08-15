<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class InventoryUploadRequest extends FormRequest
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
            'inventories' => ['required', 'file', 'mimes:csv,xls,xlsx'],
        ];
    }

    public function messages()
    {
        return [
            'inventories.required' => 'El campo Archivo de inventarios es requerido.',
            'inventories.file' => 'El campo Archivo de inventarios debe ser un archivo.',
            'inventories.mimes' => 'El Archivo de inventarios debe tener una extensión válida (csv, xls, xlsx).',
        ];
    }
}

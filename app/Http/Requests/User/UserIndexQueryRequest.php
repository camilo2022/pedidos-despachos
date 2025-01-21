<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserIndexQueryRequest extends FormRequest
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
            'column' => ['required', 'in:id,name,last_name,document_number,phone_number,address,email,password,title,zone,business_id,created_at,updated_at,deleted_at'],
            'perPage' => ['required', 'numeric'],
        ];
    }

    public function messages()
    {
        return [
            'perPage.numeric' => 'El campo Numero de registros por página debe ser un valor numérico.',
            'perPage.required' => 'El campo Numero de registros por página es requerido.'
        ];
    }

    public function attributes()
    {
        return [
            'column' => 'columna de la tabla usuarios',
            'perPage' => 'numero de páginas',
        ];
    }
}

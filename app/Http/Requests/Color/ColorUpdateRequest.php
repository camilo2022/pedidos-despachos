<?php

namespace App\Http\Requests\Color;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ColorUpdateRequest extends FormRequest
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
            'name' => ['required', 'string', 'unique:colors,name,' . $this->route('id') .',id', 'max:255'],
            'code' => ['required', 'string', 'unique:colors,code,' . $this->route('id') .',id', 'max:255']
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El campo Nombre del color es requerido.',
            'name.string' => 'El campo Nombre del color debe ser una cadena de texto.',
            'name.unique' => 'El campo Nombre del color ya existe en la base de datos.',
            'name.max' => 'El campo Nombre del color no debe exceder los 255 caracteres.',
            'code.required' => 'El campo Codigo del color es requerido.',
            'code.string' => 'El campo Codigo del color debe ser una cadena de texto.',
            'code.unique' => 'El campo Codigo del color ya existe en la base de datos.',
            'code.max' => 'El campo Codigo del color no debe exceder los 255 caracteres.',
        ];
    }
}

<?php

namespace App\Http\Requests\Correria;

use App\Rules\DateNotBetween;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CorreriaUpdateRequest extends FormRequest
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
            'name' => ['required', 'string', 'unique:correrias,name,' . $this->route('id') .',id'],
            'code' => ['required', 'string', 'unique:correrias,code,' . $this->route('id') .',id'],
            'start_date' => ['required', 'date', new DateNotBetween('correrias', 'start_date', 'end_date', $this->input('start_date'), $this->route('id'))],
            'end_date' => ['required', 'date', 'after_or_equal:start_date', new DateNotBetween('correrias', 'start_date', 'end_date', $this->input('end_date'), $this->route('id'))]
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El campo Nombre de la correria es requerido.',
            'name.string' => 'El campo Nombre de la correria debe ser una cadena de texto.',
            'name.unique' => 'El campo Nombre de la correria ya existe en la base de datos.',
            'code.required' => 'El campo Codigo de la correria es requerido.',
            'code.string' => 'El campo Codigo de la correria debe ser una cadena de texto.',
            'code.unique' => 'El campo Codigo de la correria ya existe en la base de datos.',
            'start_date.required' => 'El campo Fecha de inicio es requerido.',
            'start_date.date' => 'El campo Fecha de inicio debe ser una fecha válida.',
            'end_date.date' => 'El campo Fecha de fin debe ser una fecha válida.',
            'end_date.after_or_equal' => 'El campo Fecha de fin debe ser igual o posterior a la Fecha de inicio.'
        ];
    }
}

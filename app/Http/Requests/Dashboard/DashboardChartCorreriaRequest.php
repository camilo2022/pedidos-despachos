<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DashboardChartCorreriaRequest extends FormRequest
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
            'correria_id' => ['nullable', 'exists:correrias,id']
        ];
    }

    public function messages()
    {
        return [
            'correria_id.required' => 'El Identificador de la correria es requerido.',
            'correria_id.exists' => 'El Identificador de la correria producto no es válido.'
        ];
    }
}

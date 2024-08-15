<?php

namespace App\Http\Requests\OrderDispatchDetail;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class OrderDispatchDetailIndexQueryRequest extends FormRequest
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
            'order_dispatch_id' => ['required', 'exists:order_dispatches,id'],
        ];
    }

    public function messages()
    {
        return [
            'order_dispatch_id.required' => 'El Identificador de la orden de despacho es requerido.',
            'order_dispatch_id.exists' => 'El Identificador de la orden de despacho no es válido.',
        ];
    }
}

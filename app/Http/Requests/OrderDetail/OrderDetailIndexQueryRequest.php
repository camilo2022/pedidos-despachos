<?php

namespace App\Http\Requests\OrderDetail;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class OrderDetailIndexQueryRequest extends FormRequest
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
            'order_id' => ['required', 'exists:orders,id'],
        ];
    }

    public function messages()
    {
        return [
            'order_id.required' => 'El Identificador del pedido es requerido.',
            'order_id.exists' => 'El Identificador del pedido no es válido.',
        ];
    }
}

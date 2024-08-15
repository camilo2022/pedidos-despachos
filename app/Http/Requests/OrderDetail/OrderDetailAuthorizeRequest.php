<?php

namespace App\Http\Requests\OrderDetail;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class OrderDetailAuthorizeRequest extends FormRequest
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
            'id' => ['required', 'exists:order_details,id'],
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'El campo Detalle del pedido es requerido.',
            'id.exists' => 'El Identificador del detalle del pedido no es valido.',
        ];
    }
}

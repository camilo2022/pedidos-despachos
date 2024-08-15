<?php

namespace App\Http\Requests\OrderDispatch;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class OrderDispatchPackingRequest extends FormRequest
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
            'id' => ['required', 'exists:order_dispatches,id', 'unique:order_packings,order_dispatch_id'],
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'El Identificador de la orden de despacho es requerido.',
            'id.exists' => 'El Identificador de la orden de despacho no es válido.',
            'id.unique' => 'El identificador de la orden de despacho ya fue tomado por otro usuario para empacado.'
        ];
    }
}

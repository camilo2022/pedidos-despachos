<?php

namespace App\Http\Requests\OrderDetail;

use App\Models\OrderDetail;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class OrderDetailPendingRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Error de validación.',
            'errors' => $validator->errors()
        ], 422));
    }

    protected function prepareForValidation()
    {
        $orderDetail = OrderDetail::with('product', 'color')->findOrFail($this->input('id'));

        $this->merge([
            'status' => $orderDetail->status,
            'reference' => $orderDetail->product->code,
            'color' => $orderDetail->color->code,
        ]);
    }

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => ['required', 'exists:order_details,id'],
            'status' => [Rule::in(['Cancelado'])]
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'El campo Detalle del pedido es requerido.',
            'id.exists' => 'El Identificador del detalle del pedido no es valido.',
            'status.in' => 'El detalle del pedido con referencia ' . $this->input('reference') . ' en el color ' . $this->input('color') . ' no se puede devolver. Los detalles que se pueden devolver son aquellos que esten en los siguientes estados: Cancelado.'
        ];
    }
}

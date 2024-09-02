<?php

namespace App\Http\Requests\OrderDispatchDetail;

use App\Models\OrderDispatchDetail;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class OrderDispatchDetailPendingRequest extends FormRequest
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
        $orderDispatchDetail = OrderDispatchDetail::with(['order_detail' => ['product', 'color']])->findOrFail($this->input('id'));

        $this->merge([
            'status' => $orderDispatchDetail->status,
            'reference' => $orderDispatchDetail->order_detail->product->code,
            'color' => $orderDispatchDetail->order_detail->color->code,
        ]);
    }

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => ['required', 'exists:order_dispatch_details,id'],
            'status' => [Rule::in(['Cancelado'])]
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'El Identificador del detalle de la orden de despacho es requerido.',
            'id.exists' => 'El Identificador del detalle de la orden de despacho no es válido.',
            'status.in' => 'El detalle de la orden de despacho con referencia ' . $this->input('reference') . ' en el color ' . $this->input('color') . ' no se puede devolver. Los detalles que se pueden devolver son aquellos que esten en los siguientes estados: Cancelado.'
        ];
    }
}

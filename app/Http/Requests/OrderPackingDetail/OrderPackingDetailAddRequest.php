<?php

namespace App\Http\Requests\OrderPackingDetail;

use App\Models\OrderPackingDetail;
use App\Models\Size;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class OrderPackingDetailAddRequest extends FormRequest
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
        $sizes = Size::all()->pluck('code')->toArray();
        $orderPackingDetail = OrderPackingDetail::with('order_dispatch_detail.order_packings_details')->findOrFail($this->input('id'));

        $this->merge([
            'max' => $orderPackingDetail->order_dispatch_detail->{"T{$this->input('size')}"} - $orderPackingDetail->order_dispatch_detail->order_packings_details->where('id', '<>', $this->input('id'))->pluck("T{$this->input('size')}")->sum(),
            'sizes' => $sizes
        ]);
    }

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => ['required', 'exists:order_packing_details,id'],
            'size' => ['required', Rule::in($this->input('sizes'))],
            'quantity' => ['required', 'max:' . $this->input('max')]
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'El Identificador del detalle de la orden de alistamiento es requerido.',
            'id.exists' => 'El Identificador del detalle de la orden de alistamiento no es válido.',
            'size.required' => 'La talla es requerida.',
            'size.in' => 'La talla ingresada es invalida.',
            'quantity.required' => 'La cantidad a añadir a la talla del detalle de la orden de alistamiento es requerido.',
            'quantity.max' => "La cantidad maxima para alistar en la talla {$this->input('size')} son :max unidades." 
        ];
    }
}

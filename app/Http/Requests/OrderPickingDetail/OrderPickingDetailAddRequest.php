<?php

namespace App\Http\Requests\OrderPickingDetail;

use App\Models\OrderPickingDetail;
use App\Models\Size;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OrderPickingDetailAddRequest extends FormRequest
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
        $orderPickingDetail = OrderPickingDetail::with('order_dispatch_detail')->findOrFail($this->input('id'));

        $this->merge([
            'max' => in_array($this->input('size'), $sizes) ? $orderPickingDetail->order_dispatch_detail->{"T{$this->input('size')}"} - $orderPickingDetail->{"T{$this->input('size')}"} : 0,
            'sizes' => $sizes,
            'picking' => $orderPickingDetail->{"T{$this->input('size')}"}
        ]);
    }

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => ['required', 'exists:order_picking_details,id'],
            'size' => ['required', Rule::in($this->input('sizes'))],
            'quantity' => ['required', in_array(Auth::user()->title, ['SUPER ADMINISTRADOR', 'ADMINISTRADOR', 'COORDINADOR BODEGA']) ? '' : 'max:' . $this->input('max')]
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
            'quantity.max' => "La cantidad maxima para alistar en la talla {$this->input('size')} son {$this->input('picking')} unidades." 
        ];
    }
}

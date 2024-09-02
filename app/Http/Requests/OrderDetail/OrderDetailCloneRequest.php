<?php

namespace App\Http\Requests\OrderDetail;

use App\Models\Color;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class OrderDetailCloneRequest extends FormRequest
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
        $rules = [
            'order_id' => ['required', 'exists:orders,id'],
            'order_detail_id' => ['required', 'exists:order_details,id'],
            'items' => ['required', 'array'],
            'items.*' => ['required', 'array'],
            'items.*.color_id' => ['required', 'exists:colors,id']
        ];

        foreach ($this->input('items') as $key => $item) {
            $rules["items.$key.product_id"] = ['required', 'exists:products,id', 'unique:order_details,product_id,NULL,id,order_id,' . $this->input('order_id') . ',color_id,' . $item['color_id']];
        }

        return $rules;
    }

    public function messages()
    {
        $messages = [
            'order_id.required' => 'El Identificador del Pedido es requerido.',
            'order_id.exists' => 'El Identificador del Pedido no es valido.',
            'order_detail_id.required' => 'El Identificador del detalle del Pedido es requerido.',
            'order_detail_id.exists' => 'El Identificador del detalle del Pedido no es valido.',
            'items.required' => 'El campo Referencias seleccionadas es requerido.',
            'items.array' => 'El campo Referencias seleccionadas debe ser un arreglo.',
            'items.*.required' => 'El campo Referencia seleccionada es requerido.',
            'items.*.array' => 'El campo Referencia seleccionada debe ser un arreglo.',
            'items.*.color_id.required' => 'El Identificador del Color es requerido.',
            'items.*.color_id.exists' => 'El Identificador del Color no es valido.',
        ];

        foreach ($this->input('items') as $key => $item) {
            $product = Product::find($item['product_id']);
            $color = Color::find($item['color_id']);
            $messages["items.$key.product_id.required"] = 'El Identificador del la referencia ' . ($product->code ?? '') . ' es requerido.';
            $messages["items.$key.product_id.exists"] = 'El Identificador del la referencia ' . ($product->code ?? '') . ' no es valido.';
            $messages["items.$key.product_id.unique"] = 'La referencia ' . ($product->code ?? '') . ' en el color ' . ($color->code ?? '') . ' ya ha sido tomado en otro detalle.';
        }

        return $messages;
    }
}

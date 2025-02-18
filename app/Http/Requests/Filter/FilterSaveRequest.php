<?php

namespace App\Http\Requests\Filter;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class FilterSaveRequest extends FormRequest
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
            'product_id' => ['required', 'exists:products,id'],
            'color_id' => ['required', 'exists:colors,id'],
            'order_details' => ['required', 'array'],
            'order_details.*' => ['required', 'array'],
            'order_details.*.order_detail_id' => ['required', 'numeric', 'exists:order_details,id'],
            'order_details.*.T04' => ['required', 'numeric'],
            'order_details.*.T06' => ['required', 'numeric'],
            'order_details.*.T08' => ['required', 'numeric'],
            'order_details.*.T10' => ['required', 'numeric'],
            'order_details.*.T12' => ['required', 'numeric'],
            'order_details.*.T14' => ['required', 'numeric'],
            'order_details.*.T16' => ['required', 'numeric'],
            'order_details.*.T18' => ['required', 'numeric'],
            'order_details.*.T20' => ['required', 'numeric'],
            'order_details.*.T22' => ['required', 'numeric'],
            'order_details.*.T24' => ['required', 'numeric'],
            'order_details.*.T26' => ['required', 'numeric'],
            'order_details.*.T28' => ['required', 'numeric'],
            'order_details.*.T30' => ['required', 'numeric'],
            'order_details.*.T32' => ['required', 'numeric'],
            'order_details.*.T34' => ['required', 'numeric'],
            'order_details.*.T36' => ['required', 'numeric'],
            'order_details.*.T38' => ['required', 'numeric'],
            'order_details.*.TXXS' => ['required', 'numeric'],
            'order_details.*.TXS' => ['required', 'numeric'],
            'order_details.*.TS' => ['required', 'numeric'],
            'order_details.*.TM' => ['required', 'numeric'],
            'order_details.*.TL' => ['required', 'numeric'],
            'order_details.*.TXL' => ['required', 'numeric'],
            'order_details.*.TXXL' => ['required', 'numeric'],
        ];
    }

    public function messages()
    {
        return [
            'product_id.required' => 'El Identificador del producto es requerido.',
            'product_id.exists' => 'El Identificador del producto no es valido.',
            'color_id.required' => 'El Identificador del color es requerido.',
            'color_id.exists' => 'El Identificador del color no es valido.',
            'order_details.required' => 'Los detalles de los pedidos a filtrar es requerido.',
            'order_details.array' => 'Los detalles de los pedidos a filtrar deben ser un arreglo.',
            'order_details.*.required' => 'El item :position de los detalles de los pedidos a filtrar es requerido.',
            'order_details.*.array' => 'El item :position de los detalles de los pedidos a filtrar deben ser un arreglo.',
            'order_details.*.T04.required' => 'La talla T04 es requerida en el item :position de los detalles de los pedidos a filtrar.',
            'order_details.*.T04.numeric' => 'La talla T04 en el item :position de los detalles de los pedidos a filtrar debe ser un valor numerico.',
            'order_details.*.T06.required' => 'La talla T06 es requerida en el item :position de los detalles de los pedidos a filtrar.',
            'order_details.*.T06.numeric' => 'La talla T06 en el item :position de los detalles de los pedidos a filtrar debe ser un valor numerico.',
            'order_details.*.T08.required' => 'La talla T08 es requerida en el item :position de los detalles de los pedidos a filtrar.',
            'order_details.*.T08.numeric' => 'La talla T08 en el item :position de los detalles de los pedidos a filtrar debe ser un valor numerico.',
            'order_details.*.T10.required' => 'La talla T10 es requerida en el item :position de los detalles de los pedidos a filtrar.',
            'order_details.*.T10.numeric' => 'La talla T10 en el item :position de los detalles de los pedidos a filtrar debe ser un valor numerico.',
            'order_details.*.T12.required' => 'La talla T12 es requerida en el item :position de los detalles de los pedidos a filtrar.',
            'order_details.*.T12.numeric' => 'La talla T12 en el item :position de los detalles de los pedidos a filtrar debe ser un valor numerico.',
            'order_details.*.T14.required' => 'La talla T14 es requerida en el item :position de los detalles de los pedidos a filtrar.',
            'order_details.*.T14.numeric' => 'La talla T14 en el item :position de los detalles de los pedidos a filtrar debe ser un valor numerico.',
            'order_details.*.T16.required' => 'La talla T16 es requerida en el item :position de los detalles de los pedidos a filtrar.',
            'order_details.*.T16.numeric' => 'La talla T16 en el item :position de los detalles de los pedidos a filtrar debe ser un valor numerico.',
            'order_details.*.T18.required' => 'La talla T18 es requerida en el item :position de los detalles de los pedidos a filtrar.',
            'order_details.*.T18.numeric' => 'La talla T18 en el item :position de los detalles de los pedidos a filtrar debe ser un valor numerico.',
            'order_details.*.T20.required' => 'La talla T20 es requerida en el item :position de los detalles de los pedidos a filtrar.',
            'order_details.*.T20.numeric' => 'La talla T20 en el item :position de los detalles de los pedidos a filtrar debe ser un valor numerico.',
            'order_details.*.T22.required' => 'La talla T22 es requerida en el item :position de los detalles de los pedidos a filtrar.',
            'order_details.*.T22.numeric' => 'La talla T22 en el item :position de los detalles de los pedidos a filtrar debe ser un valor numerico.',
            'order_details.*.T24.required' => 'La talla T24 es requerida en el item :position de los detalles de los pedidos a filtrar.',
            'order_details.*.T24.numeric' => 'La talla T24 en el item :position de los detalles de los pedidos a filtrar debe ser un valor numerico.',
            'order_details.*.T26.required' => 'La talla T26 es requerida en el item :position de los detalles de los pedidos a filtrar.',
            'order_details.*.T26.numeric' => 'La talla T26 en el item :position de los detalles de los pedidos a filtrar debe ser un valor numerico.',
            'order_details.*.T28.required' => 'La talla T28 es requerida en el item :position de los detalles de los pedidos a filtrar.',
            'order_details.*.T28.numeric' => 'La talla T28 en el item :position de los detalles de los pedidos a filtrar debe ser un valor numerico.',
            'order_details.*.T30.required' => 'La talla T30 es requerida en el item :position de los detalles de los pedidos a filtrar.',
            'order_details.*.T30.numeric' => 'La talla T30 en el item :position de los detalles de los pedidos a filtrar debe ser un valor numerico.',
            'order_details.*.T32.required' => 'La talla T32 es requerida en el item :position de los detalles de los pedidos a filtrar.',
            'order_details.*.T32.numeric' => 'La talla T32 en el item :position de los detalles de los pedidos a filtrar debe ser un valor numerico.',
            'order_details.*.T34.required' => 'La talla T34 es requerida en el item :position de los detalles de los pedidos a filtrar.',
            'order_details.*.T34.numeric' => 'La talla T34 en el item :position de los detalles de los pedidos a filtrar debe ser un valor numerico.',
            'order_details.*.T36.required' => 'La talla T36 es requerida en el item :position de los detalles de los pedidos a filtrar.',
            'order_details.*.T36.numeric' => 'La talla T36 en el item :position de los detalles de los pedidos a filtrar debe ser un valor numerico.',
            'order_details.*.T38.required' => 'La talla T38 es requerida en el item :position de los detalles de los pedidos a filtrar.',
            'order_details.*.T38.numeric' => 'La talla T38 en el item :position de los detalles de los pedidos a filtrar debe ser un valor numerico.',
            'order_details.*.TXXS.required' => 'La talla TXXS es requerida en el item :position de los detalles de los pedidos a filtrar.',
            'order_details.*.TXXS.numeric' => 'La talla TXXS en el item :position de los detalles de los pedidos a filtrar debe ser un valor numerico.',
            'order_details.*.TXS.required' => 'La talla TXS es requerida en el item :position de los detalles de los pedidos a filtrar.',
            'order_details.*.TXS.numeric' => 'La talla TXS en el item :position de los detalles de los pedidos a filtrar debe ser un valor numerico.',
            'order_details.*.TS.required' => 'La talla TS es requerida en el item :position de los detalles de los pedidos a filtrar.',
            'order_details.*.TS.numeric' => 'La talla TS en el item :position de los detalles de los pedidos a filtrar debe ser un valor numerico.',
            'order_details.*.TM.required' => 'La talla TM es requerida en el item :position de los detalles de los pedidos a filtrar.',
            'order_details.*.TM.numeric' => 'La talla TM en el item :position de los detalles de los pedidos a filtrar debe ser un valor numerico.',
            'order_details.*.TL.required' => 'La talla TL es requerida en el item :position de los detalles de los pedidos a filtrar.',
            'order_details.*.TL.numeric' => 'La talla TL en el item :position de los detalles de los pedidos a filtrar debe ser un valor numerico.',
            'order_details.*.TXL.required' => 'La talla TXL es requerida en el item :position de los detalles de los pedidos a filtrar.',
            'order_details.*.TXL.numeric' => 'La talla TXL en el item :position de los detalles de los pedidos a filtrar debe ser un valor numerico.',
            'order_details.*.TXXL.required' => 'La talla TXXL es requerida en el item :position de los detalles de los pedidos a filtrar.',
            'order_details.*.TXXL.numeric' => 'La talla TXXL en el item :position de los detalles de los pedidos a filtrar debe ser un valor numerico.',
        ];
    }
}

<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class WarehouseUpdateRequest extends FormRequest
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
        $this->merge([
            'to_cut' => $this->input('to_cut') === 'true',
            'to_transit' => $this->input('to_transit') === 'true',
            'to_discount' => $this->input('to_discount') === 'true',
            'to_exclusive' => $this->input('to_exclusive') === 'true'
        ]);
    }
    
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'unique:warehouses,code,' . $this->route('id') .',id', 'max:255'],
            'to_cut' => ['required', 'boolean'],
            'to_transit' => ['required', 'boolean'],
            'to_discount' => ['required', 'boolean'],
            'to_exclusive' => ['required', 'boolean']
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El campo Nombre de la bodega es requerido.',
            'name.string' => 'El campo Nombre de la bodega debe ser una cadena de texto.',
            'name.max' => 'El campo Nombre de la bodega no debe exceder los 255 caracteres.',
            'code.required' => 'El campo Codigo de la bodega es requerido.',
            'code.string' => 'El campo Codigo de la bodega debe ser una cadena de texto.',
            'code.unique' => 'El campo Codigo de la bodega ya existe en la base de datos.',
            'code.max' => 'El campo Codigo de la bodega no debe exceder los 255 caracteres.',
            'to_cut.required' => 'El campo Bodega curva corte original es requerido.',
            'to_cut.boolean' => 'El campo Bodega curva corte original debe ser true o false.',
            'to_transit.required' => 'El campo Bodega con inventario en proceso es requerido.',
            'to_transit.boolean' => 'El campo Bodega con inventario en proceso debe ser true o false.',
            'to_discount.required' => 'El campo Bodega con inventario para filtro es requerido.',
            'to_discount.boolean' => 'El campo Bodega con inventario para filtro debe ser true o false.',            
            'to_exclusive.required' => 'El campo Bodega con inventario exclusivo para pedidos especiales es requerido.',
            'to_exclusive.boolean' => 'El campo Bodega con inventario exclusivo para pedidos especiales ser true o false.'
        ];
    }
}

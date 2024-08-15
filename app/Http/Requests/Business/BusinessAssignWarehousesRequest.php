<?php

namespace App\Http\Requests\Business;

use App\Models\Business;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BusinessAssignWarehousesRequest extends FormRequest
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
            'business_warehouse' => $this->input('warehouse_id'),
        ]);
    }
    
    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {
        return [
            'business_id' => ['required', 'exists:businesses,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'business_warehouse' => ['unique:model_warehouses,warehouse_id,NULL,id,model_id,' . $this->input('business_id') . ',model_type,' . Business::class]
        ];
    }

    public function messages()
    {
        return [
            'business_id.required' => 'El Identificador de la sucursal de la empresa es requerido.',
            'business_id.exists' => 'El Identificador de la sucursal de la empresa no es valido.',
            'warehouse_id.required' => 'El Identificador de la bodega es requerido.',
            'warehouse_id.exists' => 'El Identificador de la bodega no es valido.',
            'business_warehouse.unique' => 'La sucursal de la empresa ya tiene asignada la bodega.'
        ];
    }
}

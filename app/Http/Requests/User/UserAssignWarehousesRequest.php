<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserAssignWarehousesRequest extends FormRequest
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
            'user_warehouse' => $this->input('warehouse_id'),
        ]);
    }
    
    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'user_warehouse' => ['unique:model_warehouses,warehouse_id,NULL,id,model_id,' . $this->input('user_id') . ',model_type,' . User::class]
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => 'El Identificador del usuario es requerido.',
            'user_id.exists' => 'El Identificador del usuario no es valido.',
            'warehouse_id.required' => 'El Identificador de la bodega es requerido.',
            'warehouse_id.exists' => 'El Identificador de la bodega no es valido.',
            'user_warehouse.unique' => 'El usuario ya tiene asignada la bodega.'
        ];
    }
}

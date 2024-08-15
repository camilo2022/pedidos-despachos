<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRemoveRolesAndPermissionsRequest extends FormRequest
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
            'id' => ['required', 'exists:users,id'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'El Identificador del usuario es requerido.',
            'id.exists' => 'El Identificador del usuario no existe en la base de datos.',
            'role.required' => 'El campo rol es requerido.',
            'role.string' => 'El campo rol debe ser una cadena de texto.',
            'role.exists' => 'El rol no existe en la base de datos.',
            'permissions.required' => 'Debe seleccionar los permisos que desea asignar.',
            'permissions.array' => 'El campo permisos a asignar debe ser un arreglo.',
            'permissions.*.string' => 'El campo permiso debe ser una cadena de texto.',
            'permissions.*.exists' => 'El permiso no existe en la base de datos.'
        ];
    }
}

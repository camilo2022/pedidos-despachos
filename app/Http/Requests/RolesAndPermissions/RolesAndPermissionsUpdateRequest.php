<?php
namespace App\Http\Requests\RolesAndPermissions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class RolesAndPermissionsUpdateRequest extends FormRequest
{
    /**
     * Maneja una solicitud fallida de validación.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        // Lanzar una excepción de validación con los errores de validación obtenidos
        throw new HttpResponseException(response()->json([
            'message' => 'Error de validación',
            'errors' => $validator->errors()
        ], 422));
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'role' => ['required', 'string', 'max:255', 'unique:roles,name,' . $this->route('id') .',id'],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages()
    {
        return [
            'role.required' => 'El campo rol es requerido.',
            'role.string' => 'El campo rol debe ser una cadena de caracteres.',
            'role.max' => 'El campo rol no debe exceder los 255 caracteres.',
            'role.unique' => 'El rol ya existe en la base de datos.',
            'permissions.required' => 'El campo Permisos del rol es requerido.',
            'permissions.array' => 'El campo Permisos del rol debe ser un arreglo.',
            'permissions.*.required' => 'El permiso es requerido.',
            'permissions.*.string' => 'El permiso debe ser una cadena de caracteres.',
            'permissions.*.max' => 'El permiso no debe exceder los 255 caracteres.',
            'permissions.*.unique' => 'El permiso ya existe en la base de datos.',
        ];
    }
}

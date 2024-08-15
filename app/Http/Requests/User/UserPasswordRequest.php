<?php
namespace App\Http\Requests\User;

use App\Rules\Equals;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class UserPasswordRequest extends FormRequest
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
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'min:6']
        ];
    }

    public function messages()
    {
        return [
            'password.required' => 'El campo Contraseña del usuario es requerido.',
            'password.string' => 'El campo Contraseña del usuario debe ser una cadena de caracteres.',
            'password.min' => 'El campo Contraseña del usuario debe tener al menos 6 caracteres.',
            'password.confirmed' => 'El campo Contraseña del usuario no coincide con el campo Confirmación de contraseña del usuario.',
            'password_confirmation.required' => 'El campo Confirmacion de contraseña del usuario es requerido',
            'password_confirmation.string' => 'El campo Confirmacion de contraseña del usuario debe ser una cadena de caracteres.',
            'password_confirmation.min' => 'El campo Confirmacion de contraseña del usuario debe tener al menos 6 caracteres.',
        ];
    }
}

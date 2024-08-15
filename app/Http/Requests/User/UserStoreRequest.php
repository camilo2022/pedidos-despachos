<?php
namespace App\Http\Requests\User;

use App\Traits\Titles;
use App\Traits\Zones;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UserStoreRequest extends FormRequest
{
    use Titles;
    use Zones;
    
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
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'document_number' => ['required', 'string', 'min:5', 'max:20', 'unique:users'],
            'phone_number' => ['required', 'string', 'size:10'],
            'address' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'min:6'],
            'title' => ['required', 'string', Rule::in($this->titles())],
            'zone' => ['required', 'string', Rule::in($this->zones())]
        ];
    }

    public function messages()
    {
        return [
            'required' => 'El campo :attribute es requerido.',
            'string' => 'El campo :attribute debe ser una cadena de caracteres.',
            'email' => 'El campo :attribute debe ser una dirección de correo electrónico válida.',
            'unique' => 'El campo :attribute ya ha sido tomado.',
            'max' => 'El campo :attribute no debe exceder los :max caracteres.',
            'min' => 'El campo :attribute debe tener al menos :min caracteres.',
            'size' => 'El campo :attribute debe tener :size caracteres.',
            'confirmed' => 'El campo :attribute no coincide con la confirmación de contraseña.',
            'in' => 'El campo :attribute es invalido.'
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'nombre',
            'last_name' => 'apellido',
            'document_number' => 'numero de documento',
            'phone_number' => 'numero de telefono',
            'address' => 'direccion',
            'email' => 'correo electrónico',
            'password' => 'contraseña',
            'password_confirmation' => 'confirmación de contraseña',
            'title' => 'titulo',
            'zone' => 'zona'
        ];
    }
}

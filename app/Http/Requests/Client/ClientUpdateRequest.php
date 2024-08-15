<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class ClientUpdateRequest extends FormRequest
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
            'client_branch_name' => $this->input('client_branch_name') ?? $this->input('client_name'),
            'client_branch_code' => sprintf('%03d', $this->input('client_branch_code')),
            'client_branch_address' => $this->input('client_branch_address') ?? $this->input('client_address')
        ]);
    }

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'client_name' => ['required', 'string', 'max:255'],
            'client_address' => ['required', 'string', 'max:255'],
            'client_number_document' => ['required', 'string', 'max:30'],
            'client_number_phone' => ['nullable', 'min:5', 'max:15'],
            'client_branch_code' => ['required', 'string', 'size:3', 'unique:clients,client_branch_code,' . $this->route('id') . ',id,client_number_document,' . $this->input('client_number_document')],
            'client_branch_name' => ['required', 'string', 'max:255'],
            'client_branch_address' => ['required', 'string', 'max:255'],
            'client_branch_number_phone' => ['nullable', 'min:5', 'max:15'],
            'country' => ['required', 'exists:countries,name'],
            'departament' => ['required', 'exists:departaments,name'],
            'city' => ['required', 'exists:cities,name'],
            'number_phone' => ['nullable', 'min:5', 'max:15'],
            'email' => ['nullable', 'email', 'max:100'],
            'zone' => ['nullable', 'string', 'max:30'],
            //'type' => ['required', 'string', Rule::in(['DEBITO', 'CREDITO'])]
        ];
    }

    public function messages()
    {
        return [
            'client_name.required' => 'El campo Nombre del cliente es requerido.',
            'client_name.string' => 'El campo Nombre del cliente debe ser una cadena de caracteres.',
            'client_name.max' => 'El campo Nombre del cliente no debe exceder los 255 caracteres.',
            'client_address.required' => 'El campo Direccion del cliente es requerido.',
            'client_address.string' => 'El campo Direccion del cliente debe ser una cadena de caracteres.',
            'client_address.max' => 'El campo Direccion del cliente no debe exceder los 255 caracteres.',
            'client_number_document.required' => 'El campo Numero de documento del cliente es requerido.',
            'client_number_document.string' => 'El campo Numero de documento del cliente debe ser una cadena de caracteres.',
            'client_number_document.max' => 'El campo Numero de documento del cliente no debe exceder los 30 caracteres.',
            'client_number_phone.min' => 'El campo Numero de telefono del cliente debe tener minimo 5 digitos.',
            'client_number_phone.max' => 'El campo Numero de telefono del cliente no debe exceder los 15 digitos.',
            'client_branch_code.required' => 'El campo Codigo de la sucursal es requerido.',
            'client_branch_code.string' => 'El campo Codigo de la sucursal debe ser una cadena de caracteres.',
            'client_branch_code.max' => 'El campo Codigo de la sucursal debe tener 3 digitos.',
            'client_branch_code.unique' => 'El Codigo de la sucursal ya ha sido tomado.',            
            'client_branch_name.required' => 'El campo Nombre de la sucursal es requerido.',
            'client_branch_name.string' => 'El campo Nombre de la sucursal debe ser una cadena de caracteres.',
            'client_branch_name.max' => 'El campo Nombre de la sucursal no debe exceder los 255 caracteres.',
            'client_branch_address.required' => 'El campo Direccion de despacho de la sucursal es requerido.',
            'client_branch_address.string' => 'El campo Direccion de despacho de la sucursal debe ser una cadena de caracteres.',
            'client_branch_address.max' => 'El campo Direccion de despacho de la sucursal no debe exceder los 255 caracteres.',
            'client_branch_number_phone.min' => 'El campo Numero de telefono de la sucursal debe tener minimo 5 digitos.',
            'client_branch_number_phone.max' => 'El campo Numero de telefono de la sucursal no debe exceder los 15 digitos.',
            'country.required' => 'El campo Pais es requerido.',
            'country.exists' => 'El Pais no es valido.',
            'departament.required' => 'El campo Departamento es requerido.',
            'departament.exists' => 'El Departamento no es valido.',
            'city.required' => 'El campo Ciudad es requerido.',
            'city.exists' => 'La ciudad no es valido.',            
            'number_phone.min' => 'El campo Numero de telefono debe tener minimo 5 digitos.',
            'number_phone.max' => 'El campo Numero de telefono no debe exceder los 15 digitos.',
            'email.email' => 'El campo Correo electronico debe ser una dirección de correo electrónico válida.',
            'email.max' => 'El campo Correo electronico no debe exceder los 100 caracteres.',
            'zone.string' => 'El campo Zona debe ser una cadena de caracteres.',
            'zone.max' => 'El campo Zona no debe exceder los 30 caracteres.',
            'type.required' => 'El campo Tipo de cliente es requerido.',
            'type.string' => 'El campo Tipo de cliente debe ser una cadena de caracteres.',
            'type.in' => 'El campo Tipo de cliente es invalido.'
        ];
    }
}

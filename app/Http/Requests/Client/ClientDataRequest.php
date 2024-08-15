<?php

namespace App\Http\Requests\Client;

use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ClientDataRequest extends FormRequest
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
        $client = Client::with('wallet', 'compra', 'cartera', 'bodega', 'administrador', 'chamber_of_commerce','rut', 'identity_card', 'signature_warranty')->findOrFail($this->input('client_id'));
        $this->merge([
            'client' => $client,
            'compra' => json_decode($this->input('compra'), true),
            'cartera' => json_decode($this->input('cartera'), true),
            'bodega' => json_decode($this->input('bodega'), true),
            'administrador' => json_decode($this->input('administrador'), true)
        ]);
    }

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'chamber_of_commerce' => [is_null($this->input('client')->chamber_of_commerce) && ($this->input('client')->type == 'CREDITO' || is_null($this->input('client')->type)) ? 'required' : 'nullable', 'file', 'mimes:pdf'],
            'rut' => [is_null($this->input('client')->chamber_of_commerce) && ($this->input('client')->type == 'CREDITO' || is_null($this->input('client')->type)) ? 'required' : 'nullable', 'file', 'mimes:pdf'],
            'identity_card' => [is_null($this->input('client')->chamber_of_commerce) ? 'required' : 'nullable', 'file', 'mimes:jpeg,jpg,png,pdf'],
            'signature_warranty' => [is_null($this->input('client')->chamber_of_commerce) && ($this->input('client')->type == 'CREDITO' || is_null($this->input('client')->type)) ? 'required' : 'nullable', 'file', 'mimes:jpeg,jpg,png,pdf'],
            'client_id' => ['required', 'exists:clients,id'],            
            'compra' => [is_null($this->input('client')->compra) ? 'required' : 'nullable', 'array:name,last_name,phone_number,email'],
            'compra.name' => ['required', 'string', 'max:255'],
            'compra.last_name' => ['required', 'string', 'max:255'],
            'compra.phone_number' => ['required', 'string', 'min:6', 'max:15'],
            'compra.email' => ['required', 'email', 'max:255'],
            'cartera' => [is_null($this->input('client')->cartera) ? 'required' : 'nullable', 'array:name,last_name,phone_number,email'],
            'cartera.name' => ['required', 'string', 'max:255'],
            'cartera.last_name' => ['required', 'string', 'max:255'],
            'cartera.phone_number' => ['required', 'string', 'min:6', 'max:15'],
            'cartera.email' => ['required', 'email', 'max:255'],
            'bodega' => [is_null($this->input('client')->bodega) ? 'required' : 'nullable', 'array:name,last_name,phone_number,email'],
            'bodega.name' => ['required', 'string', 'max:255'],
            'bodega.last_name' => ['required', 'string', 'max:255'],
            'bodega.phone_number' => ['required', 'string', 'min:6', 'max:15'],
            'bodega.email' => ['required', 'email', 'max:255'],
            'administrador' => [is_null($this->input('client')->administrador) ? 'required' : 'nullable', 'array:name,last_name,phone_number,email'],
            'administrador.name' => ['required', 'string', 'max:255'],
            'administrador.last_name' => ['required', 'string', 'max:255'],
            'administrador.phone_number' => ['required', 'string', 'min:6', 'max:15'],
            'administrador.email' => ['required', 'email', 'max:255']
        ];
    }

    public function messages()
    {
        return [
            'chamber_of_commerce.required' => 'El campo Archivo camara de comercio es requerido.',
            'chamber_of_commerce.file' => 'El campo Archivo camara de comercio debe ser un archivo.',
            'chamber_of_commerce.mimes' => 'El Archivo camara de comercio debe tener una extensión válida (pdf).',
            'rut.required' => 'El campo Archivo RUT es requerido.',
            'rut.file' => 'El campo Archivo RUT debe ser un archivo.',
            'rut.mimes' => 'El Archivo RUT debe tener una extensión válida (pdf).',
            'identity_card.required' => 'El campo Archivo documento de identificacion es requerido.',      
            'identity_card.file' => 'El campo Archivo documento de identificacion debe ser un archivo.',
            'identity_card.mimes' => 'El Archivo documento de identificacion debe tener una extensión válida (jpeg, jpg, png, pdf).',
            'signature_warranty.required' => 'El campo Archivo firma de garantia es requerido.',   
            'signature_warranty.file' => 'El campo Archivo firma de garantia debe ser un archivo.',
            'signature_warranty.mimes' => 'El Archivo firma de garantia debe tener una extensión válida (jpeg, jpg, png, pdf).',
            'client_id.required' => 'El Identificador del cliente es requerido.',
            'client_id.exists' => 'El Identificador del cliente no es valido.',
            'compra.array' => 'La referencia personal/comercial de tipo COMPRA debe ser un arreglo.',
            'compra.name.required' => 'El campo Nombre de referencia personal/comercial de tipo COMPRA es requerido.',
            'compra.name.string' => 'El campo Nombre de referencia personal/comercial de tipo COMPRA debe ser una cadena de caracteres.',
            'compra.name.max' => 'El campo Nombre de referencia personal/comercial de tipo COMPRA no debe exceder los 255 caracteres.',
            'compra.last_name.required' => 'El campo Apellido de referencia personal/comercial de tipo COMPRA es requerido.',
            'compra.last_name.string' => 'El campo Apellido de referencia personal/comercial de tipo COMPRA debe ser una cadena de caracteres.',
            'compra.last_name.max' => 'El campo Apellido de referencia personal/comercial de tipo COMPRA no debe exceder los 255 caracteres.',
            'compra.phone_number.required' => 'El campo Telefono de referencia personal/comercial de tipo COMPRA es requerido.',
            'compra.phone_number.string' => 'El campo Telefono de referencia personal/comercial de tipo COMPRA debe ser una cadena de caracteres.',
            'compra.phone_number.min' => 'El campo Telefono de referencia personal/comercial de tipo COMPRA debe tener minimo 6 caracteres.',
            'compra.phone_number.max' => 'El campo Telefono de referencia personal/comercial de tipo COMPRA no debe exceder los 15 caracteres.',
            'compra.email.required' => 'El campo Correo electronico de referencia personal/comercial de tipo COMPRA es requerido.',
            'compra.email.email' => 'El campo Correo electronico de referencia personal/comercial de tipo COMPRA debe ser una dirección de correo electrónico válida.',
            'compra.email.max' => 'El campo Correo electronico de referencia personal/comercial de tipo COMPRA no debe exceder los 255 caracteres.',
            'cartera.array' => 'La referencia personal/comercial de tipo CARTERA debe ser un arreglo.',
            'cartera.name.required' => 'El campo Nombre de referencia personal/comercial de tipo CARTERA es requerido.',
            'cartera.name.string' => 'El campo Nombre de referencia personal/comercial de tipo CARTERA debe ser una cadena de caracteres.',
            'cartera.name.max' => 'El campo Nombre de referencia personal/comercial de tipo CARTERA no debe exceder los 255 caracteres.',
            'cartera.last_name.required' => 'El campo Apellido de referencia personal/comercial de tipo CARTERA es requerido.',
            'cartera.last_name.string' => 'El campo Apellido de referencia personal/comercial de tipo CARTERA debe ser una cadena de caracteres.',
            'cartera.last_name.max' => 'El campo Apellido de referencia personal/comercial de tipo CARTERA no debe exceder los 255 caracteres.',
            'cartera.phone_number.required' => 'El campo Telefono de referencia personal/comercial de tipo CARTERA es requerido.',
            'cartera.phone_number.string' => 'El campo Telefono de referencia personal/comercial de tipo CARTERA debe ser una cadena de caracteres.',
            'cartera.phone_number.min' => 'El campo Telefono de referencia personal/comercial de tipo CARTERA debe tener minimo 6 caracteres.',
            'cartera.phone_number.max' => 'El campo Telefono de referencia personal/comercial de tipo CARTERA no debe exceder los 15 caracteres.',
            'cartera.email.required' => 'El campo Correo electronico de referencia personal/comercial de tipo CARTERA es requerido.',
            'cartera.email.email' => 'El campo Correo electronico de referencia personal/comercial de tipo CARTERA debe ser una dirección de correo electrónico válida.',
            'cartera.email.max' => 'El campo Correo electronico de referencia personal/comercial de tipo CARTERA no debe exceder los 255 caracteres.',
            'bodega.array' => 'La referencia personal/comercial de tipo BODEGA debe ser un arreglo.',
            'bodega.name.required' => 'El campo Nombre de referencia personal/comercial de tipo BODEGA es requerido.',
            'bodega.name.string' => 'El campo Nombre de referencia personal/comercial de tipo BODEGA debe ser una cadena de caracteres.',
            'bodega.name.max' => 'El campo Nombre de referencia personal/comercial de tipo BODEGA no debe exceder los 255 caracteres.',
            'bodega.last_name.required' => 'El campo Apellido de referencia personal/comercial de tipo BODEGA es requerido.',
            'bodega.last_name.string' => 'El campo Apellido de referencia personal/comercial de tipo BODEGA debe ser una cadena de caracteres.',
            'bodega.last_name.max' => 'El campo Apellido de referencia personal/comercial de tipo BODEGA no debe exceder los 255 caracteres.',
            'bodega.phone_number.required' => 'El campo Telefono de referencia personal/comercial de tipo BODEGA es requerido.',
            'bodega.phone_number.string' => 'El campo Telefono de referencia personal/comercial de tipo BODEGA debe ser una cadena de caracteres.',
            'bodega.phone_number.min' => 'El campo Telefono de referencia personal/comercial de tipo BODEGA debe tener minimo 6 caracteres.',
            'bodega.phone_number.max' => 'El campo Telefono de referencia personal/comercial de tipo BODEGA no debe exceder los 15 caracteres.',
            'bodega.email.required' => 'El campo Correo electronico de referencia personal/comercial de tipo BODEGA es requerido.',
            'bodega.email.email' => 'El campo Correo electronico de referencia personal/comercial de tipo BODEGA debe ser una dirección de correo electrónico válida.',
            'bodega.email.max' => 'El campo Correo electronico de referencia personal/comercial de tipo BODEGA no debe exceder los 255 caracteres.',
            'administrador.array' => 'La referencia personal/comercial de tipo ADMINISTRADOR debe ser un arreglo.',
            'administrador.name.required' => 'El campo Nombre de referencia personal/comercial de tipo ADMINISTRADOR es requerido.',
            'administrador.name.string' => 'El campo Nombre de referencia personal/comercial de tipo ADMINISTRADOR debe ser una cadena de caracteres.',
            'administrador.name.max' => 'El campo Nombre de referencia personal/comercial de tipo ADMINISTRADOR no debe exceder los 255 caracteres.',
            'administrador.last_name.required' => 'El campo Apellido de referencia personal/comercial de tipo ADMINISTRADOR es requerido.',
            'administrador.last_name.string' => 'El campo Apellido de referencia personal/comercial de tipo ADMINISTRADOR debe ser una cadena de caracteres.',
            'administrador.last_name.max' => 'El campo Apellido de referencia personal/comercial de tipo ADMINISTRADOR no debe exceder los 255 caracteres.',
            'administrador.phone_number.required' => 'El campo Telefono de referencia personal/comercial de tipo ADMINISTRADOR es requerido.',
            'administrador.phone_number.string' => 'El campo Telefono de referencia personal/comercial de tipo ADMINISTRADOR debe ser una cadena de caracteres.',
            'administrador.phone_number.min' => 'El campo Telefono de referencia personal/comercial de tipo ADMINISTRADOR debe tener minimo 6 caracteres.',
            'administrador.phone_number.max' => 'El campo Telefono de referencia personal/comercial de tipo ADMINISTRADOR no debe exceder los 15 caracteres.',
            'administrador.email.required' => 'El campo Correo electronico de referencia personal/comercial de tipo ADMINISTRADOR es requerido.',
            'administrador.email.email' => 'El campo Correo electronico de referencia personal/comercial de tipo ADMINISTRADOR debe ser una dirección de correo electrónico válida.',
            'administrador.email.max' => 'El campo Correo electronico de referencia personal/comercial de tipo ADMINISTRADOR no debe exceder los 255 caracteres.'
        ];
    }
}

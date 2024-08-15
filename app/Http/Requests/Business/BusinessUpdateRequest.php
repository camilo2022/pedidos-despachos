<?php

namespace App\Http\Requests\Business;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BusinessUpdateRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'branch' => ['required', 'string', 'unique:businesses,branch,' . $this->route('id') .',id', 'max:255'],
            'nit' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'exists:countries,name', 'max:50'],
            'departament' => ['required', 'string', 'exists:departaments,name', 'max:50'],
            'city' => ['required', 'string', 'exists:cities,name', 'max:50'],
            'address' => ['required', 'string', 'max:255'],
            'order_footer' => ['required', 'string', 'max:1000'],
            'order_notify_email' => ['nullable', 'email', 'max:100'],
            'dispatch_footer' => ['required', 'string', 'max:1000'],
            'packing_footer' => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El campo Nombre de la empresa es requerido.',
            'name.string' => 'El campo Nombre de la empresa debe ser una cadena de caracteres.',
            'name.max' => 'El campo Nombre de la empresa no debe exceder los 255 caracteres.',
            'branch.required' => 'El campo Sucursal es requerido.',
            'branch.string' => 'El campo Sucursal debe ser una cadena de caracteres.',
            'branch.unique' => 'El campo Sucursal ya ha sido tomado.',
            'branch.max' => 'El campo Sucursal no debe exceder los 255 caracteres.',
            'nit.required' => 'El campo Nit es requerido.',
            'nit.string' => 'El campo Nit debe ser una cadena de caracteres.',
            'nit.max' => 'El campo Nit no debe exceder los 20 caracteres.',
            'country.required' => 'El campo País es requerido.',
            'country.string' => 'El campo País debe ser una cadena de caracteres.',
            'country.exists' => 'El campo País no es válido.',
            'country.max' => 'El campo País no debe exceder los 50 caracteres.',
            'departament.required' => 'El campo Departamento es requerido.',
            'departament.string' => 'El campo Departamento debe ser una cadena de caracteres.',
            'departament.exists' => 'El campo Departamento no es válido.',
            'departament.max' => 'El campo Departamento no debe exceder los 50 caracteres.',
            'city.required' => 'El campo Ciudad es requerido.',
            'city.string' => 'El campo Ciudad debe ser una cadena de caracteres.',
            'city.exists' => 'El campo Ciudad no es válido.',
            'city.max' => 'El campo Ciudad no debe exceder los 50 caracteres.',
            'address.required' => 'El campo Dirección es requerido.',
            'address.string' => 'El campo Dirección debe ser una cadena de caracteres.',
            'address.max' => 'El campo Dirección no debe exceder los 255 caracteres.',
            'order_footer.required' => 'El campo Pie de página de la orden es requerido.',
            'order_footer.string' => 'El campo Pie de página de la orden debe ser una cadena de caracteres.',
            'order_footer.max' => 'El campo Pie de página de la orden no debe exceder los 1000 caracteres.',
            'order_notify_email.email' => 'El campo Correo electronico de notificacion de pedido asentado debe ser una dirección de correo electrónico válida.',
            'order_notify_email.max' => 'El campo Correo electronico de notificacion de pedido asentado no debe exceder los 100 caracteres.',
            'dispatch_footer.required' => 'El campo Pie de página del despacho es requerido.',
            'dispatch_footer.string' => 'El campo Pie de página del despacho debe ser una cadena de caracteres.',
            'dispatch_footer.max' => 'El campo Pie de página del despacho no debe exceder los 1000 caracteres.',
            'packing_footer.required' => 'El campo Pie de página del embalaje es requerido.',
            'packing_footer.string' => 'El campo Pie de página del embalaje debe ser una cadena de caracteres.',
            'packing_footer.max' => 'El campo Pie de página del embalaje no debe exceder los 1000 caracteres.'
        ];
    }
}

<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class OrderObservationRequest extends FormRequest
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
            'wallet_dispatch_percentage' => $this->input('wallet_dispatch_official') + $this->input('wallet_dispatch_document')
        ]);
    }

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => ['required', 'exists:orders,id'],
            'wallet_observation' => ['nullable', 'string', 'max:255'],
            'wallet_dispatch_official' => ['required', 'numeric', 'min:0', 'max:100'],
            'wallet_dispatch_document' => ['required', 'numeric', 'min:0', 'max:100'],
            'wallet_dispatch_percentage' => ['required', 'numeric', 'size:100']
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'El Identificador del pedido es requerido.',
            'id.exists' => 'El Identificador del pedido no es valido.',
            'wallet_observation.string' => 'El campo Observacion de cartera debe ser una cadena de caracteres.',
            'wallet_observation.max' => 'El campo Observacion de cartera no debe exceder los 255 caracteres.',
            'wallet_dispatch_official.required' => 'La condicion de despacho oficial es requerida.',
            'wallet_dispatch_official.numeric' => 'La condicion de despacho oficial debe ser un valor numerico.',
            'wallet_dispatch_official.min' => 'La condicion de despacho oficial minima es de 0%.',
            'wallet_dispatch_official.min' => 'La condicion de despacho oficial maxima es de 100%.',
            'wallet_dispatch_document.required' => 'La condicion de despacho documento es requerida.',
            'wallet_dispatch_document.numeric' => 'La condicion de despacho documento debe ser un valor numerico.',
            'wallet_dispatch_document.min' => 'La condicion de despacho documento minima es de 0%.',
            'wallet_dispatch_document.min' => 'La condicion de despacho documento maxima es de 100%.',
            'wallet_dispatch_percentage.required' => 'La condicion de despacho es requerida.',
            'wallet_dispatch_percentage.numeric' => 'La condicion de despacho debe ser un valor numerico.',
            'wallet_dispatch_percentage.size' => 'La condicion de despacho no puede exceder el 100%.'
        ];
    }
}

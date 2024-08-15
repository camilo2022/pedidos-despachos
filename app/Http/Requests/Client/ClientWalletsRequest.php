<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ClientWalletsRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Error de validación',
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
            'wallets.*' => ['required'],
            'wallets.*.documento' => ['required', 'exists:clients,client_number_document'],
            'wallets.*.cero_a_treinta' => ['nullable', 'numeric'],
            'wallets.*.uno_a_treinta' => ['nullable', 'numeric'],
            'wallets.*.treintayuno_a_sesenta' => ['nullable', 'numeric'],
            'wallets.*.sesentayuno_a_noventa' => ['nullable', 'numeric'],
            'wallets.*.noventayuno_a_cientoveinte' => ['nullable', 'numeric'],
            'wallets.*.cientoveintiuno_a_cientocincuenta' => ['nullable', 'numeric'],
            'wallets.*.cientocincuentayuno_a_cientoochenta' => ['nullable', 'numeric'],
            'wallets.*.mayor_a_cientoochentayuno' => ['nullable', 'numeric'],
            'wallets.*.total' => ['nullable', 'numeric'],
        ];
    }
    
    public function messages()
    {
        return [
            'wallets.*.required' => 'La fila #:position debe contener los siguientes datos documento, cero_a_treinta, uno_a_treinta, treintayuno_a_sesenta, sesentayuno_a_noventa, noventayuno_a_cientoveinte, cientoveintiuno_a_cientocincuenta, cientocincuentayuno_a_cientoochenta, mayor_a_cientoochentayuno, total.',
            'wallets.*.documento.required' => 'Fila #:position el Numero de documento del cliente es requerido.',
            'wallets.*.documento.exists' => 'Fila #:position el Numero de documento del cliente no es valido.',
            'wallets.*.cero_a_treinta.numeric' => 'Fila #:position el campo deuda de cero a treinta debe ser numerico.',
            'wallets.*.uno_a_treinta.numeric' => 'Fila #:position el campo deuda de uno a treinta debe ser numerico.',
            'wallets.*.treintayuno_a_sesenta.numeric' => 'Fila #:position el campo deuda de cero a treinta debe ser numerico.',
            'wallets.*.sesentayuno_a_noventa.numeric' => 'Fila #:position el campo deuda de sesenta y uno a noventa debe ser numerico.',
            'wallets.*.noventayuno_a_cientoveinte.numeric' => 'Fila #:position el campo deuda de noventa y uno a ciento veinte debe ser numerico.',
            'wallets.*.cientoveintiuno_a_cientocincuenta.numeric' => 'Fila #:position el campo deuda de ciento veintiuno a ciento cincuenta debe ser numerico.',
            'wallets.*.cientocincuentayuno_a_cientoochenta.numeric' => 'Fila #:position el campo deuda de ciento cincuenta y uno a ciento ochenta debe ser numerico.',
            'wallets.*.mayor_a_cientoochentayuno.numeric' => 'Fila #:position el campo deuda mayor a ciento ochenta y uno debe ser numerico.',
            'wallets.*.total.numeric' => 'Fila #:position el campo total de la deuda debe ser numerico.'
        ];
    }
}

<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ClientWalletRequest extends FormRequest
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
            'total' => $this->input('zero_to_thirty') + $this->input('one_to_thirty') + $this->input('thirty_one_to_sixty') + $this->input('sixty_one_to_ninety')
            + $this->input('ninety_one_to_one_hundred_twenty') + $this->input('one_hundred_twenty_one_to_one_hundred_fifty') + $this->input('one_hundred_fifty_one_to_one_hundred_eighty_one')
            + $this->input('eldest_to_one_hundred_eighty_one'),
        ]);
    }

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'client_id' => ['required', 'exists:clients,id'],
            'zero_to_thirty' => ['required', 'numeric'],
            'one_to_thirty' => ['required', 'numeric'],
            'thirty_one_to_sixty' => ['required', 'numeric'],
            'sixty_one_to_ninety' => ['required', 'numeric'],
            'ninety_one_to_one_hundred_twenty' => ['required', 'numeric'],
            'one_hundred_twenty_one_to_one_hundred_fifty' => ['required', 'numeric'],
            'one_hundred_fifty_one_to_one_hundred_eighty_one' => ['required', 'numeric'],
            'eldest_to_one_hundred_eighty_one' => ['required', 'numeric']
        ];
    }

    public function messages()
    {
        return [
            'client_id.required' => 'El Identificador del cliente es requerido.',
            'client_id.exists' => 'El Identificador del cliente no es valido.',
            'zero_to_thirty.required' => 'El campo deuda de cero a treinta es requerido.',
            'zero_to_thirty.numeric' => 'El campo deuda de cero a treinta debe ser numerico.',
            'one_to_thirty.required' => 'El campo deuda de uno a treinta es requerido.',
            'one_to_thirty.numeric' => 'El campo deuda de uno a treinta debe ser numerico.',
            'thirty_one_to_sixty.required' => 'El campo deuda de treinta y uno a sesenta es requerido.',
            'thirty_one_to_sixty.numeric' => 'El campo deuda de cero a treinta debe ser numerico.',
            'sixty_one_to_ninety.required' => 'El campo deuda de sesenta y uno a noventa es requerido.',
            'sixty_one_to_ninety.numeric' => 'El campo deuda de sesenta y uno a noventa debe ser numerico.',
            'ninety_one_to_one_hundred_twenty.required' => 'El campo deuda de noventa y uno a ciento veinte es requerido.',
            'ninety_one_to_one_hundred_twenty.numeric' => 'El campo deuda de noventa y uno a ciento veinte debe ser numerico.',
            'one_hundred_twenty_one_to_one_hundred_fifty.required' => 'El campo deuda de ciento veintiuno a ciento cincuenta es requerido.',
            'one_hundred_twenty_one_to_one_hundred_fifty.numeric' => 'El campo deuda de ciento veintiuno a ciento cincuenta debe ser numerico.',
            'one_hundred_fifty_one_to_one_hundred_eighty_one.required' => 'El campo deuda de ciento cincuenta y uno a ciento ochenta es requerido.',
            'one_hundred_fifty_one_to_one_hundred_eighty_one.numeric' => 'El campo deuda de ciento cincuenta y uno a ciento ochenta debe ser numerico.',
            'eldest_to_one_hundred_eighty_one.required' => 'El campo deuda mayor a ciento ochenta y uno es requerido.',
            'eldest_to_one_hundred_eighty_one.numeric' => 'El campo deuda mayor a ciento ochenta y uno debe ser numerico.'
        ];
    }
}

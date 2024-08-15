<?php

namespace App\Http\Requests\Order;

use App\Models\Order;
use App\Models\Wallet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class OrderApproveRequest extends FormRequest
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
        $order = Order::with('order_details', 'seller_user', 'client')->findOrFail($this->input('id'));
        $wallet = Wallet::where('number_document', $order->client->client_number_document)->first();
        $quota = 0;

        if($wallet) {
            $quota += $wallet->thirty_one_to_sixty + $wallet->sixty_one_to_ninety + $wallet->ninety_one_to_one_hundred_twenty + $wallet->one_hundred_twenty_one_to_one_hundred_fifty + $wallet->one_hundred_fifty_one_to_one_hundred_eighty_one + $wallet->eldest_to_one_hundred_eighty_one;
        }

        $this->merge([
            'quota' => $quota,
            'wallet' => $wallet
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
            'quota' => ['required', 'integer', 'max:0']
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'El Identificador del pedido es requerido.',
            'id.exists' => 'El Identificador del pedido no es valido.',
            'quota.required' => 'El valor de deuda es requerido.',
            'quota.max' => 'El pedido no puede ser asentado debido a que presenta deudas pendientes en los siguientes períodos:
            
            Deuda de 1 a 30 días: $' . number_format(is_null($this->input('wallet')) ? 0 : $this->input('wallet')->one_to_thirty, 0, ',', '.') . '. 
            Deuda de 31 a 60 días: $' . number_format(is_null($this->input('wallet')) ? 0 : $this->input('wallet')->thirty_one_to_sixty, 0, ',', '.') . '. 
            Deuda de 61 a 90 días: $' . number_format(is_null($this->input('wallet')) ? 0 : $this->input('wallet')->sixty_one_to_ninety, 0, ',', '.') . '. 
            Deuda de 91 a 120 días: $' . number_format(is_null($this->input('wallet')) ? 0 : $this->input('wallet')->ninety_one_to_one_hundred_twenty, 0, ',', '.') . '. 
            Deuda de 121 a 150 días: $' . number_format(is_null($this->input('wallet')) ? 0 : $this->input('wallet')->one_hundred_twenty_one_to_one_hundred_fifty, 0, ',', '.') . '. 
            Deuda de 151 a 180 días: $' . number_format(is_null($this->input('wallet')) ? 0 : $this->input('wallet')->one_hundred_fifty_one_to_one_hundred_eighty_one, 0, ',', '.') . '. 
            Deuda mayor a 181 días: $' . number_format(is_null($this->input('wallet')) ? 0 : $this->input('wallet')->eldest_to_one_hundred_eighty_one, 0, ',', '.') . '. 
            
            Deuda total: $' . number_format($this->input('quota') + (is_null($this->input('wallet')) ? 0 : $this->input('wallet')->one_to_thirty), 0, ',', '.') . '. 
            
            Para realizacion de pagos o mas consultas sobre el estado de su cartera comunicarse al correo gestiondecobranza@organizacionbless.com.co o llame al +57 321 7770013.'
        ];
    }
}

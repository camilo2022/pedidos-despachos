<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class InvoiceStoreRequest extends FormRequest
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
            'person_id' => ['required', 'exists:people,id'],
            'invoice_details' => ['required', 'array'],
            'invoice_details.*' => ['required', 'array'],
            'invoice_details.*.warehouse_id' => ['required', 'exists:warehouses,id'],
            'invoice_details.*.product_id' => ['required', 'exists:products,id'],
            'invoice_details.*.size_id' => ['nullable', 'exists:sizes,id'],
            'invoice_details.*.color_id' => ['nullable', 'exists:colors,id'],
            'invoice_details.*.quantity' => ['required', 'numeric', 'min:1'],
            'invoice_details.*.promotion_id' => ['nullable', 'exists:promotions,id'],
            'invoice_details.*.price' => ['required', 'numeric'],
            'invoice_details.*.discount' => ['required', 'numeric'],
            'invoice_details.*.subtotal' => ['required', 'numeric'],
            'invoice_details.*.total' => ['required', 'numeric'],
            'payments' => ['required', 'array'],
            'payments.*' => ['required', 'array'],
            'payments.*.payment_method_id' => ['required', 'exists:payment_methods,id'],
            'payments.*.payment' => ['required', 'numeric', ''],
        ];
    }

    public function messages()
    {
        return [
            'person_id.required' => 'El Identificador del Pedido es requerido.',
            'person_id.exists' => 'El Identificador del Pedido no es valido.',
        ];
    }
}

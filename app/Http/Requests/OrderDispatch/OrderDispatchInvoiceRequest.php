<?php

namespace App\Http\Requests\OrderDispatch;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class OrderDispatchInvoiceRequest extends FormRequest
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
            'id' => ['required', 'exists:order_dispatches,id'],
            'invoices' => ['required', 'array'],
            'invoices.*.reference' => ['required', 'string', 'max:30'],
            'invoices.*.supports' => ['nullable', 'array'],
            'invoices.*.supports.*' => ['file', 'mimes:jpeg,jpg,png,gif,pdf,txt,docx,xlsx,xlsm,xlsb,xltx'],
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'El Identificador de la orden de despacho es requerido.',
            'id.exists' => 'El Identificador de la orden de despacho no es válido.',
            'invoices.required' => 'El campo Facturas de la orden de despacho es requerido.',
            'invoices.array' => 'El campo Facturas de la orden de despacho debe ser un arreglo.',
            'invoices.*.reference.required' => 'El campo Referencia de la factura de la orden de despacho es requerido.',
            'invoices.*.reference.string' => 'El campo Referencia de la factura debe ser una cadena de caracteres.',
            'invoices.*.reference.max' => 'El campo Referencia de la factura de la orden de despacho no debe exceder los 30 caracteres.',
            'invoices.*.supports.required' => 'El campo Soporte de la Facturas de la orden de despacho es requerido.',
            'invoices.*.supports.array' => 'El campo Soporte de la Facturas de la orden de despacho debe ser un arreglo.',
            'invoices.*.supports.*.file' => 'El Soporte de la Factura #:position debe ser un archivo.',
            'invoices.*.supports.*.mimes' => 'El Soporte de la Factura #:position debe tener una extensión válida (jpeg, jpg, png, gif, pdf, txt, docx, xlsx, xlsm, xlsb, xltx).'
        ];
    }
}

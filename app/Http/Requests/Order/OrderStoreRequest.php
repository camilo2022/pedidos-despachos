<?php

namespace App\Http\Requests\Order;

use App\Models\Correria;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderStoreRequest extends FormRequest
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
        DB::statement('CALL correrias(?,?)', [Carbon::now()->format('Y-m-d H:i:s'), Auth::user()->business_id]);
        $correria = Correria::where('business_id', Auth::user()->business_id)->first();

        $this->merge([
            'seller_dispatch_percentage' => $this->input('seller_dispatch_official') + $this->input('seller_dispatch_document'),
            'correria_id' => $correria ? $correria->id : null
        ]);
    }

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'order_id' => ['nullable', 'exists:orders,id'],
            'client_id' => ['required', 'exists:clients,id'],
            'dispatch_type' => ['required', 'string', 'max:255', Rule::in(['De inmediato', 'Antes de', 'Despues de', 'Total', 'Semanal'])],
            'dispatch_date' => ['required_if:dispatch_type,Antes de', 'required_if:dispatch_type,Despues de', 'date', 'after_or_equal:' . Carbon::parse($this->input('dispatch_date'))->format('Y-m-d')],
            'seller_observation' => ['nullable', 'string', 'max:255'],
            'seller_dispatch_official' => ['required', 'numeric', 'min:0', 'max:100'],
            'seller_dispatch_document' => ['required', 'numeric', 'min:0', 'max:100'],
            'seller_dispatch_percentage' => ['required', 'numeric', 'size:100'],
            'correria_id' => ['required', 'exists:correrias,id']
        ];
    }

    public function messages()
    {
        return [
            'order_id.exists' => 'El Identificador del pedido no es valido.',
            'client_id.required' => 'El Identificador del cliente es requerido.',
            'client_id.exists' => 'El Identificador del cliente no es valido.',
            'dispatch_type.required' => 'El Tipo de despacho es requerido.',
            'dispatch_type.string' => 'El Tipo de despacho debe ser una cadena de caracteres.',
            'dispatch_type.max' => 'El Tipo de despacho no debe exceder los 255 caracteres.',
            'dispatch_type.in' => 'El Tipo de despacho no es valido.',
            'dispatch_date.required' => 'La Fecha de despacho es requerido.',
            'dispatch_date.date' => 'La Fecha de despacho debe ser una fecha valida.',
            'dispatch_date.after_or_equal' => 'La Fecha de despacho debe ser posterior o igual a la fecha actual.',
            'seller_observation.string' => 'La Observacion del asesor debe ser una cadena de caracteres.',
            'seller_observation.max' => 'La Observacion del asesor no debe exceder los 255 caracteres.',
            'seller_dispatch_official.required' => 'La condicion de despacho oficial es requerida.',
            'seller_dispatch_official.numeric' => 'La condicion de despacho oficial debe ser un valor numerico.',
            'seller_dispatch_official.min' => 'La condicion de despacho oficial minima es de 0%.',
            'seller_dispatch_official.min' => 'La condicion de despacho oficial maxima es de 100%.',
            'seller_dispatch_document.required' => 'La condicion de despacho documento es requerida.',
            'seller_dispatch_document.numeric' => 'La condicion de despacho documento debe ser un valor numerico.',
            'seller_dispatch_document.min' => 'La condicion de despacho documento minima es de 0%.',
            'seller_dispatch_document.min' => 'La condicion de despacho documento maxima es de 100%.',
            'seller_dispatch_percentage.required' => 'La condicion de despacho es requerida.',
            'seller_dispatch_percentage.numeric' => 'La condicion de despacho debe ser un valor numerico.',
            'seller_dispatch_percentage.size' => 'La condicion de despacho no puede exceder el 100%.',
            'correria_id.required' => 'El Identificador de la Correria es requerido. No existe una correria activa en la fecha actual.',
            'correria_id.exists' => 'El Identificador de la correria no es valido.',
        ];
    }
}

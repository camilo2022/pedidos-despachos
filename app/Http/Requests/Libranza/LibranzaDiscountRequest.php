<?php

namespace App\Http\Requests\Libranza;

use App\Models\Libranza;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LibranzaDiscountRequest extends FormRequest
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
        $libranza = Libranza::with('libranza_discounts')->findOrFail($this->input('id'));

        $this->merge([
            'max' => $libranza->value - $libranza->libranza_discounts->sum('value')
        ]);
    }

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => ['required', 'exists:libranzas,id'],
            'value' => ['required', 'numeric', 'min:1000', 'max:' . $this->input('max')]
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'El Identificador de la libranza es requerido.',
            'id.exists' => 'El Identificador de la libranza no es valido.',
            'value.required' => 'El campo Valor a descontar es requerido.',
            'value.numeric' => 'El campo Valor a descontar debe ser numerico.',
            'value.min' => 'El valor minimo a descontar requerido debe ser de :min COP.',
            'value.max' => 'El valor maximo a descontar requerido no debe exceder los :max COP.',
        ];
    }
}

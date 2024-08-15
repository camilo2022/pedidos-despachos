<?php

namespace App\Http\Requests\OrderDetail;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class OrderDetailEditRequest extends FormRequest
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
            'product_id' => ['nullable', 'exists:products,id'],
            'color_id' => ['nullable', 'exists:colors,id'],
            'tone_id' => ['nullable', 'exists:tones,id'],
        ];
    }

    public function messages()
    {
        return [
            'product_id.exists' => 'El Identificador del producto no es valido.',
            'color_id.exists' => 'El Identificador del color no es valido.',
            'tone_id.exists' => 'El Identificador del tono no es valido.',
        ];
    }
}

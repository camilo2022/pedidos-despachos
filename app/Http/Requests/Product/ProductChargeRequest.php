<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductChargeRequest extends FormRequest
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
            'photos' => json_decode($this->input('photos')),
            'videos' => json_decode($this->input('videos'))
        ]);
    }

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'photo' => ['nullable', 'file', 'mimes:jpeg,jpg,png'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['nullable', 'file', 'mimes:jpeg,jpg,png'],
            'videos' => ['nullable', 'array'],
            'videos.*' => ['nullable', 'file', 'mimes:mp4,webm']
        ];
    }

    public function messages()
    {
        return [
            'product_id.required' => 'El Identificador del producto es requerido.',
            'product_id.exists' => 'El Identificador del producto no es valido.',
            'photo.file' => 'El campo Foto principal del producto es requerido.',
            'photo.mimes' => 'El Archivo foto principal debe tener una extensión válida (jpeg, jpg, png).',
            'photos.required' => 'El campo Fotos del producto es requerido.',
            'photos.array' => 'El campo Fotos del producto debe ser un arreglo.',
            'photos.*.mimes' => 'El Archivo foto debe tener una extensión válida (jpeg, jpg, png).',
            'videos.required' => 'El campo Videos del producto es requerido.',
            'videos.array' => 'El campo Videos del producto debe ser un arreglo.',
            'videos.*.mimes' => 'El Archivo foto debe tener una extensión válida (mp4, webm).',
        ];
    }
}

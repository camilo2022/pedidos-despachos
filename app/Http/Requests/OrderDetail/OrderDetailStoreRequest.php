<?php

namespace App\Http\Requests\OrderDetail;

use App\Models\Color;
use App\Models\Inventory;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Size;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderDetailStoreRequest extends FormRequest
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
        $sizes = Size::all();  

        $user = User::with('warehouses')->findOrFail(Auth::user()->id);

        $users = User::with('warehouses')->whereHas('warehouses', fn($query) => $query->whereIn('warehouses.id', $user->warehouses->pluck('id')->toArray()))->get();

        $inventory = Inventory::select('products.trademark AS MARCA', 'products.code AS REFERENCIA', 'colors.name AS COLOR');
        foreach ($sizes as $size) {
            $inventory->addSelect(DB::raw("COALESCE(SUM(CASE WHEN sizes.code = '$size->code' THEN inventories.quantity ELSE 0 END), 0) AS T$size->code"));
        }
        $inventory->join('warehouses', 'warehouses.id', 'inventories.warehouse_id')
        ->join('products', 'products.id', 'inventories.product_id')
        ->join('colors', 'colors.id', 'inventories.color_id')
        ->join('sizes', 'sizes.id', 'inventories.size_id')
        ->where('quantity', '>', 0)
        ->whereIn('warehouse_id', $user->warehouses->pluck('id')->toArray())
        ->where('product_id', $this->input('product_id'))
        ->where('color_id', $this->input('color_id'))
        ->groupBy('products.trademark', 'products.code', 'colors.name');
        
        $inventory = $inventory->first();
        
        $committed = OrderDetail::select('products.trademark AS MARCA', 'products.code AS REFERENCIA', 'colors.name AS COLOR');
        foreach ($sizes as $size) {
            $committed->addSelect(DB::raw("SUM(T$size->code) as T$size->code"));
        }

        $committed->join('orders', 'orders.id', 'order_details.order_id')
        ->join('users', 'users.id', 'orders.seller_user_id')
        ->join('products', 'products.id', 'order_details.product_id')
        ->join('colors', 'colors.id', 'order_details.color_id')
        ->where('product_id', $this->input('product_id'))
        ->where('color_id', $this->input('color_id'))
        ->where('orders.business_id', Auth::user()->business_id)       
        ->when(in_array(Auth::user()->title, ['VENDEDOR ESPECIAL']),
            function ($query) {
                $query->whereIn('orders.seller_status', ['Aprobado'])
                ->whereIn('orders.wallet_status', ['Pendiente', 'Autorizado'])
                ->whereIn('order_details.status', ['Pendiente', 'Autorizado'])
                ->where('users.title', 'VENDEDOR ESPECIAL');
            }
        )
        ->when(!in_array(Auth::user()->title, ['VENDEDOR ESPECIAL']),
            function ($query) {
                $query->whereIn('orders.seller_status', ['Aprobado'])
                ->whereIn('orders.wallet_status', ['Pendiente', 'Aprobado', 'Parcialmente Aprobado'])
                ->whereIn('order_details.status', ['Pendiente', 'Aprobado', 'Comprometido'])
                ->whereNot('users.title', 'VENDEDOR ESPECIAL');
            }
        )
        ->whereIn('orders.seller_user_id', $users->pluck('id')->toArray())
        ->groupBy('products.trademark', 'products.code', 'colors.name');

        $committed = $committed->first();

        if(empty($committed)) {
            $committed = (object) [];
            $product = Product::findOrFail($this->input('product_id'));
            $color = Color::findOrFail($this->input('color_id'));
            $committed->MARCA = $product->trademark;
            $committed->REFERENCIA = $product->code;
            $committed->COLOR = $color->name;
            foreach ($sizes as $size) {
                $committed->{"T{$size->code}"} = 0;
            }
        }
                
        if(empty($inventory)) {
            $inventory = (object) [];
            $product = Product::findOrFail($this->input('product_id'));
            $color = Color::findOrFail($this->input('color_id'));
            $inventory->MARCA = $product->trademark;
            $inventory->REFERENCIA = $product->code;
            $inventory->COLOR = $color->name;
            foreach ($sizes as $size) {
                $inventory->{"T{$size->code}"} = 0;
            }
        }

        $quantity = 0;

        foreach ($sizes as $size) {
            $quantity += $this->input("T{$size->code}");
            $this->merge([
                "T{$size->code}_min" => 0,
                "T{$size->code}_max" => $inventory->{"T{$size->code}"} - $committed->{"T{$size->code}"},
            ]);
        }

        $this->merge([
            'quantity' => $quantity
        ]);
    }

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'order_id' => ['required', 'exists:orders,id'],
            'product_id' => ['required', 'exists:products,id', 'unique:order_details,product_id,NULL,id,order_id,' . $this->input('order_id') . ',color_id,' . $this->input('color_id')],
            'color_id' => ['required', 'exists:colors,id'],
            'price' => ['required', 'numeric'],
            'negotiated_price' => ['required', 'numeric'],
            'quantity' => ['required', 'numeric', 'min:1'],
            'seller_observation' => ['nullable', 'string', 'max:255']
        ];

        $sizes = Size::all();

        foreach ($sizes as $size) {
            $rules["T{$size->code}"] = ['required', 'numeric', 'min:' . $this->input("T{$size->code}_min"), 'max:' . $this->input("T{$size->code}_max")];
        }

        return $rules;
    }

    public function messages()
    {
        $messages = [
            'order_id.required' => 'El Identificador del Pedido es requerido.',
            'order_id.exists' => 'El Identificador del Pedido no es valido.',
            'product_id.required' => 'El Identificador del Producto es requerido.',
            'product_id.exists' => 'El Identificador del Producto no es valido.',
            'product_id.unique' => 'El Identificador del Producto ya ha sido tomado en otro detalle.',
            'color_id.required' => 'El Identificador del Color es requerido.',
            'color_id.exists' => 'El Identificador del Color no es valido.',
            'price.required' => 'El campo Precio del producto es requerido.',
            'price.numeric' => 'El campo Precio del producto debe ser numerico.',
            'negotiated_price.required' => 'El campo Precio negociado del producto es requerido.',
            'negotiated_price.numeric' => 'El campo Precio negociado del producto debe ser numerico.',
            'quantity.required' => 'El campo Cantidad total es requerido.',
            'quantity.numeric' => 'El campo Cantidad total debe ser numerico.',
            'quantity.min' => 'La cantidad minima requerida de unidades para registrar el detalle debe ser de :min unidad.',
            'seller_observation.string' => 'El campo Observacion del asesor debe ser una cadena de caracteres.',
            'seller_observation.max' => 'El campo Observacion del asesor no debe exceder los 255 caracteres.',
        ];

        $sizes = Size::all();

        foreach ($sizes as $size) {
            $messages["T{$size->code}.required"] = "La talla {$size->code} es requerida.";
            $messages["T{$size->code}.numeric"] = "La talla {$size->code} debe ser numerico.";
            $messages["T{$size->code}.min"] = "La cantidad minima requerida de unidades para la talla {$size->code} debe ser de :min unidad.";
            $messages["T{$size->code}.max"] = "La cantidad maxima requerida de unidades para la talla {$size->code} debe ser de :max unidad.";
        }

        return $messages;
    }
}

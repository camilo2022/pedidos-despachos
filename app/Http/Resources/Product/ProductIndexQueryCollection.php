<?php

namespace App\Http\Resources\Product;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductIndexQueryCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'products' => $this->collection->map(function ($product) {
                return [
                    'id' => $product->id,
                    'item' => $product->item,
                    'code' => $product->code,
                    'category' => $product->category,
                    'trademark' => $product->trademark,
                    'price' => $product->price,
                    'description' => $product->description,
                    'inventories' => $product->inventories,
                    'colors' => $product->inventories->pluck('color')->unique()->values(),
                    'sizes' => $product->inventories->pluck('size')->unique()->values(),
                    'warehouses' => $product->inventories->pluck('warehouse')->unique()->values(),
                    'created_at' => $this->formatDate($product->created_at),
                    'updated_at' => $this->formatDate($product->updated_at),
                    'deleted_at' => $product->deleted_at
                ];
            }),

            'meta' => [
                'pagination' => $this->paginationMeta(),
            ],
        ];
    }

    protected function formatDate($date)
    {
        return Carbon::parse($date)->format('Y-m-d H:i:s');
    }

    protected function paginationMeta()
    {
        return [
            'total' => $this->total(),
            'count' => $this->count(),
            'per_page' => $this->perPage(),
            'current_page' => $this->currentPage(),
            'total_pages' => $this->lastPage(),
        ];
    }
}

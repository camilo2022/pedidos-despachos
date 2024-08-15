<?php

namespace App\Http\Resources\Inventory;

use App\Models\Size;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class InventoryIndexQueryCollection extends ResourceCollection
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
            'inventories' => $this->collection->map(function ($inventory) {
                $inventories = [
                    'BODEGA' => $inventory->BODEGA,
                    'CODBOD' => $inventory->CODBOD,
                    'MARCA' => $inventory->MARCA,
                    'REFERENCIA' => $inventory->REFERENCIA,
                    'COLOR' =>$inventory->COLOR,
                    'CODCOL' => $inventory->CODCOL,
                    'SISTEMA' => $inventory->SISTEMA
                ];

                $sizes = Size::all();
                foreach($sizes as $size){
                    $inventories["T{$size->code}"] = $inventory->{"T{$size->code}"};
                }

                return $inventories;
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

<?php

namespace App\Http\Resources\Warehose;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class WarehouseIndexQueryCollection extends ResourceCollection
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
            'warehouses' => $this->collection->map(function ($warehouse) {
                return [
                    'id' => $warehouse->id,
                    'name' => $warehouse->name,
                    'code' => $warehouse->code,
                    'to_cut' => $warehouse->to_cut,
                    'to_transit' => $warehouse->to_transit,
                    'to_discount' => $warehouse->to_discount,
                    'to_exclusive' => $warehouse->to_exclusive,
                    'created_at' => $this->formatDate($warehouse->created_at),
                    'updated_at' => $this->formatDate($warehouse->updated_at),
                    'deleted_at' => $warehouse->deleted_at
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

<?php

namespace App\Http\Resources\Business;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BusinessIndexQueryCollection extends ResourceCollection
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
            'businesses' => $this->collection->map(function ($business) {
                return [
                    'id' => $business->id,
                    'name' => $business->name,
                    'branch' => $business->branch,
                    'number_document' => $business->number_document,
                    'country' => $business->country,
                    'departament' => $business->departament,
                    'city' => $business->city,
                    'address' => $business->address,
                    'order_footer' => $business->order_footer,
                    'dispatch_footer' => $business->dispatch_footer,
                    'packing_footer' => $business->packing_footer,
                    'created_at' => $this->formatDate($business->created_at),
                    'updated_at' => $this->formatDate($business->updated_at),
                    'deleted_at' => $business->deleted_at
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


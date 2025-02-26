<?php

namespace App\Http\Resources\Libranza;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class LibranzaIndexQueryCollection extends ResourceCollection
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
            'libranzas' => $this->collection->map(function ($libranza) {
                return [
                    'id' => $libranza->id,
                    'invoice_id' => $libranza->invoice_id,
                    'invoice' => $libranza->invoice,
                    'value' => $libranza->value,
                    'status' => $libranza->status,
                    'share' => $libranza->share,
                    'user_id' => $libranza->user_id,
                    'user' => $libranza->user,
                    'libranza_discounts' => $libranza->libranza_discounts,
                    'created_at' => $this->formatDate($libranza->created_at),
                    'updated_at' => $this->formatDate($libranza->updated_at),
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

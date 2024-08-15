<?php

namespace App\Http\Resources\Correria;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CorreriaIndexQueryCollection extends ResourceCollection
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
            'correrias' => $this->collection->map(function ($correria) {
                return [
                    'id' => $correria->id,
                    'name' => $correria->name,
                    'code' => $correria->code,
                    'start_date' => $this->formatDate($correria->start_date),
                    'end_date' => $this->formatDate($correria->end_date),
                    'business' => $correria->business,
                    'created_at' => $this->formatDate($correria->created_at),
                    'updated_at' => $this->formatDate($correria->updated_at),
                    'deleted_at' => $correria->deleted_at
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

<?php

namespace App\Http\Resources\Color;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ColorIndexQueryCollection extends ResourceCollection
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
            'colors' => $this->collection->map(function ($color) {
                return [
                    'id' => $color->id,
                    'name' => $color->name,
                    'code' => $color->code,
                    'sample' =>  is_null($color->sample) ? '' : asset('storage/' . $color->sample->path),
                    'created_at' => $this->formatDate($color->created_at),
                    'updated_at' => $this->formatDate($color->updated_at),
                    'deleted_at' => $color->deleted_at
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

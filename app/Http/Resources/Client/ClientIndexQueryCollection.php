<?php

namespace App\Http\Resources\Client;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ClientIndexQueryCollection extends ResourceCollection
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
            'clients' => $this->collection->map(function ($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'address' => $client->address,
                    'number_document' => $client->number_document,
                    'cell_number_phone' => $client->cell_number_phone,
                    'branch_code' => $client->branch_code,
                    'branch_name' => $client->branch_name,
                    'branch_address' => $client->branch_address,
                    'branch_number_phone' => $client->branch_number_phone,
                    'country' => $client->country,
                    'departament' => $client->departament,
                    'city' => $client->city,
                    'number_phone' => $client->number_phone,
                    'email' => $client->email,
                    'zone' => $client->zone,
                    'created_at' => $this->formatDate($client->created_at),
                    'updated_at' => $this->formatDate($client->updated_at),
                    'deleted_at' => $client->deleted_at
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

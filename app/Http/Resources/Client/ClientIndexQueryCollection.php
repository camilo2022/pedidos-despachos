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
                    'client_name' => $client->client_name,
                    'client_address' => $client->client_address,
                    'client_number_document' => $client->client_number_document,
                    'client_number_phone' => $client->client_number_phone,
                    'client_branch_code' => $client->client_branch_code,
                    'client_branch_name' => $client->client_branch_name,
                    'client_branch_address' => $client->client_branch_address,
                    'client_branch_number_phone' => $client->client_branch_number_phone,
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

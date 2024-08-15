<?php

namespace App\Http\Resources\OrderWallet;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderWalletIndexQueryCollection extends ResourceCollection
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
            'orderWallets' => $this->collection->map(function ($orderWallet) {
                return [
                    'id' => $orderWallet->id,
                    'client_id' => $orderWallet->client_id,
                    'client' => $orderWallet->client,
                    'client_branch_id' => $orderWallet->client_branch_id,
                    'client_branch' => $orderWallet->client_branch,
                    'dispatch' => $orderWallet->dispatch,
                    'dispatch_date' => $orderWallet->dispatch_date,
                    'seller_user_id' => $orderWallet->seller_user_id,
                    'seller_user' => $orderWallet->seller_user,
                    'seller_status' => $orderWallet->seller_status,
                    'seller_date' => $orderWallet->seller_date,
                    'seller_observation' => $orderWallet->seller_observation,
                    'wallet_user_id' => $orderWallet->wallet_user_id,
                    'wallet_user' => $orderWallet->wallet_user,
                    'wallet_status' => $orderWallet->wallet_status,
                    'wallet_date' => $orderWallet->wallet_date,
                    'wallet_observation' => $orderWallet->wallet_observation,
                    'dispatched_status' => $orderWallet->dispatched_status,
                    'dispatched_date' => $orderWallet->dispatched_date,
                    'correria_id' => $orderWallet->correria_id,
                    'correria' => $orderWallet->correria,
                    'created_at' => $this->formatDate($orderWallet->created_at),
                    'updated_at' => $this->formatDate($orderWallet->updated_at),
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

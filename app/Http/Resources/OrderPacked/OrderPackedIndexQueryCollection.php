<?php

namespace App\Http\Resources\OrderPacked;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderPackedIndexQueryCollection extends ResourceCollection
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
            'orderPackings' => $this->collection->map(function ($orderPacked) {
                return [
                    'id' => $orderPacked->id,
                    'order' => $orderPacked->order,
                    'client_id' => $orderPacked->order->client_id,
                    'client' => $orderPacked->order->client,
                    'client_branch_id' => $orderPacked->order->client_branch_id,
                    'client_branch' => $orderPacked->order->client_branch,
                    'dispatch' => $orderPacked->order->dispatch,
                    'dispatch_date' => $orderPacked->order->dispatch_date,
                    'seller_user_id' => $orderPacked->order->seller_user_id,
                    'seller_user' => $orderPacked->order->seller_user,
                    'seller_status' => $orderPacked->order->seller_status,
                    'seller_date' => $orderPacked->order->seller_date,
                    'seller_observation' => $orderPacked->order->seller_observation,
                    'wallet_user_id' => $orderPacked->order->wallet_user_id,
                    'wallet_user' => $orderPacked->order->wallet_user,
                    'wallet_status' => $orderPacked->order->wallet_status,
                    'wallet_date' => $orderPacked->order->wallet_date,
                    'wallet_observation' => $orderPacked->order->wallet_observation,
                    'dispatched_status' => $orderPacked->order->dispatched_status,
                    'dispatched_date' => $orderPacked->order->dispatched_date,
                    'correria_id' => $orderPacked->order->correria_id,
                    'correria' => $orderPacked->order->correria,
                    'dispatch_user_id' => $orderPacked->dispatch_user_id,
                    'dispatch_user' => $orderPacked->dispatch_user,
                    'dispatch_status' => $orderPacked->dispatch_status,
                    'dispatch_date' => $orderPacked->dispatch_date,
                    'consecutive' => $orderPacked->consecutive,
                    'created_at' => $this->formatDate($orderPacked->created_at),
                    'updated_at' => $this->formatDate($orderPacked->updated_at),
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

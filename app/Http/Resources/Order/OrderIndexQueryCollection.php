<?php

namespace App\Http\Resources\Order;

use App\Models\Size;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderIndexQueryCollection extends ResourceCollection
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
            'orders' => $this->collection->map(function ($order) {
                $orderSizes = collect([]);

                $sizes = Size::all();

                foreach($sizes as $size) {
                    if($order->order_details->pluck("T{$size->code}")->sum() > 0) {
                        $orderSizes = $orderSizes->push($size);
                    }
                }
                return [
                    'id' => $order->id,
                    'client_id' => $order->client_id,
                    'client' => $order->client,
                    'dispatch_type' => $order->dispatch_type,
                    'dispatch_date' => $order->dispatch_date,
                    'seller_user_id' => $order->seller_user_id,
                    'seller_user' => $order->seller_user,
                    'seller_status' => $order->seller_status,
                    'seller_date' => $order->seller_date,
                    'seller_observation' => $order->seller_observation,
                    'seller_dispatch_official' => $order->seller_dispatch_official,
                    'seller_dispatch_document' => $order->seller_dispatch_document,
                    'wallet_user_id' => $order->wallet_user_id,
                    'wallet_user' => $order->wallet_user,
                    'wallet_status' => $order->wallet_status,
                    'wallet_date' => $order->wallet_date,
                    'wallet_observation' => $order->wallet_observation,
                    'wallet_dispatch_official' => $order->wallet_dispatch_official,
                    'wallet_dispatch_document' => $order->wallet_dispatch_document,
                    'dispatch_status' => $order->dispatch_status,
                    'dispatch_date' => $order->dispatch_date,
                    'correria_id' => $order->correria_id,
                    'correria' => $order->correria,
                    'order_details' => $order->order_details,
                    'sizes' => $orderSizes->isNotEmpty() ? $orderSizes : $sizes,
                    'created_at' => $this->formatDate($order->created_at),
                    'updated_at' => $this->formatDate($order->updated_at),
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

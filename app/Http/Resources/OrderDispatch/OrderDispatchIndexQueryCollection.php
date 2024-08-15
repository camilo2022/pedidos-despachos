<?php

namespace App\Http\Resources\OrderDispatch;

use App\Models\Size;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderDispatchIndexQueryCollection extends ResourceCollection
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
            'orderDispatches' => $this->collection->map(function ($orderDispatch) {
                $orderDispatchSizes = collect([]);

                $sizes = Size::all();

                foreach($sizes as $size) {
                    if($orderDispatch->order_dispatch_details->pluck("T{$size->code}")->sum() > 0) {
                        $orderDispatchSizes = $orderDispatchSizes->push($size);
                    }
                }
                return [
                    'id' => $orderDispatch->id,
                    'client_id' => $orderDispatch->client_id,
                    'client' => $orderDispatch->client,
                    'consecutive' => $orderDispatch->consecutive,
                    'dispatch_user_id' => $orderDispatch->dispatch_user_id,
                    'dispatch_user' => $orderDispatch->dispatch_user,
                    'dispatch_status' => $orderDispatch->dispatch_status,
                    'dispatch_date' => $orderDispatch->dispatch_date,                    
                    'invoice_user_id' => $orderDispatch->invoice_user_id,
                    'invoice_user' => $orderDispatch->invoice_user,
                    'invoice_date' => $orderDispatch->invoice_date,
                    'correria_id' => $orderDispatch->correria_id,
                    'correria' => $orderDispatch->correria,
                    'business_id' => $orderDispatch->business_id,
                    'business' => $orderDispatch->business,
                    'order_picking' => $orderDispatch->order_picking,
                    'order_packing' => $orderDispatch->order_packing,
                    'order_dispatch_details' => $orderDispatch->order_dispatch_details,
                    'sizes' => $orderDispatchSizes->isNotEmpty() ? $orderDispatchSizes : $sizes,
                    'created_at' => $this->formatDate($orderDispatch->created_at),
                    'updated_at' => $this->formatDate($orderDispatch->updated_at),
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

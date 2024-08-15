<?php

namespace App\Http\Resources\OrderWallet;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderWalletPaymentIndexQueryCollection extends ResourceCollection
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
            'orderWalletPayments' => $this->collection->map(function ($orderWalletPayment) {
                return [
                    'id' => $orderWalletPayment->id,
                    'value' => $orderWalletPayment->value,
                    'reference' => $orderWalletPayment->reference,
                    'date' => $this->formatDate($orderWalletPayment->date),
                    'payment_type_id' => $orderWalletPayment->payment_type_id,
                    'payment_type' => $orderWalletPayment->payment_type,
                    'bank_id' => $orderWalletPayment->bank_id,
                    'bank' => $orderWalletPayment->bank,
                    'model' => $orderWalletPayment->model,
                    'files' => $orderWalletPayment->files->map(function ($file) {
                            return [
                                'id' => $file->id,
                                'name' => $file->name,
                                'path' => asset('storage/' . $file->path),
                                'mime' => $file->mime,
                                'extension' => $file->extension,
                                'size' => $file->size,
                                'user_id' => $file->user_id,
                                'user' => $file->user,
                                'metadata' => json_decode($file->path, true)
                            ];
                        }
                    )->toArray(),
                    'created_at' => $this->formatDate($orderWalletPayment->created_at),
                    'updated_at' => $this->formatDate($orderWalletPayment->updated_at),
                    'deleted_at' => $orderWalletPayment->deleted_at,
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

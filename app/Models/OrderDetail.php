<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as Auditing;

class OrderDetail extends Model implements Auditable
{
    use HasFactory, Auditing;

    protected $table = 'order_details';
    protected $fillable = [
        'order_id',
        'product_id',
        'color_id',
        'price',
        'negotiated_price',
        'T04',
        'T06',
        'T08',
        'T10',
        'T12',
        'T14',
        'T16',
        'T18',
        'T20',
        'T22',
        'T24',
        'T26',
        'T28',
        'T30',
        'T32',
        'T34',
        'T36',
        'T38',
        'TXXS',
        'TXS',
        'TS',
        'TM',
        'TL',
        'TXL',
        'TXXL',
        'seller_user_id',
        'seller_date',
        'seller_observation',
        'wallet_user_id',
        'wallet_date',
        'dispatch_user_id',
        'dispatched_date',
        'status'
    ];

    protected $auditInclude = [
        'order_id',
        'product_id',
        'color_id',
        'price',
        'negotiated_price',
        'T04',
        'T06',
        'T08',
        'T10',
        'T12',
        'T14',
        'T16',
        'T18',
        'T20',
        'T22',
        'T24',
        'T26',
        'T28',
        'T30',
        'T32',
        'T34',
        'T36',
        'T38',
        'TXXS',
        'TXS',
        'TS',
        'TM',
        'TL',
        'TXL',
        'TXXL',
        'seller_user_id',
        'seller_date',
        'seller_observation',
        'wallet_user_id',
        'wallet_date',
        'dispatch_user_id',
        'dispatch_date',
        'status'
    ];
    
    public function order_dispatch_detail() : HasOne
    {
        return $this->hasOne(OrderDispatchDetail::class, 'order_detail_id');
    }

    public function order() : BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product() : BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function color() : BelongsTo
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    public function seller_user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_user_id');
    }

    public function wallet_user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'wallet_user_id');
    }

    public function dispatch_user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'dispatch_user_id');
    }
}

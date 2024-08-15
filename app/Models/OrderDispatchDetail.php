<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as Auditing;

class OrderDispatchDetail extends Model implements Auditable
{
    use HasFactory, Auditing;

    protected $table = 'order_dispatch_details';
    protected $fillable = [
        'order_dispatch_id',
        'order_id',
        'order_detail_id',
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
        'user_id',
        'status',
        'date'
    ];

    protected $auditInclude = [
        'order_dispatch_id',
        'order_id',
        'order_detail_id',
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
        'user_id',
        'status',
        'date'
    ];
    
    public function order_dispatch() : BelongsTo
    {
        return $this->belongsTo(OrderDispatch::class, 'order_dispatch_id');
    }

    public function order() : BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function order_detail() : BelongsTo
    {
        return $this->belongsTo(OrderDetail::class, 'order_detail_id');
    }

    public function order_packings_details() : HasMany
    {
        return $this->hasMany(OrderPackingDetail::class, 'order_dispatch_detail_id');
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

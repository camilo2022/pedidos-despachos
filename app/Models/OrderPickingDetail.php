<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as Auditing;

class OrderPickingDetail extends Model implements Auditable
{
    use HasFactory, Auditing, SoftDeletes;

    protected $table = 'order_picking_details';
    protected $fillable = [
        'order_picking_id',
        'order_dispatch_detail_id',
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
        'status',
        'date'
    ];

    protected $auditInclude = [
        'order_picking_id',
        'order_dispatch_detail_id',
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
        'status',
        'date'
    ];

    public function order_picking() : BelongsTo
    {
        return $this->belongsTo(OrderPicking::class, 'order_picking_id');
    }

    public function order_dispatch_detail() : BelongsTo
    {
        return $this->belongsTo(OrderDispatchDetail::class, 'order_dispatch_detail_id');
    }
}

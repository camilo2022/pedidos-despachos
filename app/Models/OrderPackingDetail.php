<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as Auditing;

class OrderPackingDetail extends Model implements Auditable
{
    use HasFactory, Auditing;

    protected $table = 'order_packing_details';
    protected $fillable = [
        'order_package_id',
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
        'date'
    ];

    protected $auditInclude = [
        'order_package_id',
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
        'date'
    ];

    public function order_package() : BelongsTo
    {
        return $this->belongsTo(OrderPackage::class, 'order_package_id');
    }

    public function order_dispatch_detail() : BelongsTo
    {
        return $this->belongsTo(OrderDispatchDetail::class, 'order_dispatch_detail_id');
    }
}

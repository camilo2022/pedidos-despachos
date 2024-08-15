<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as Auditing;

class OrderPicking extends Model implements Auditable
{
    use HasFactory, SoftDeletes, Auditing;

    protected $table = 'order_pickings';
    protected $fillable = [
        'order_dispatch_id',
        'picking_user_id',
        'picking_status',
        'picking_date'
    ];

    protected $auditInclude = [
        'order_dispatch_id',
        'picking_user_id',
        'picking_status',
        'picking_date'
    ];

    public function order_picking_details() : HasMany
    {
        return $this->hasMany(OrderPickingDetail::class, 'order_picking_id');
    }

    public function order_dispatch() : BelongsTo
    {
        return $this->belongsTo(OrderDispatch::class, 'order_dispatch_id');
    }

    public function picking_user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'picking_user_id');
    }
}

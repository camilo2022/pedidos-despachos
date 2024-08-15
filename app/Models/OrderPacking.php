<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as Auditing;

class OrderPacking extends Model implements Auditable
{
    use HasFactory, Auditing;

    protected $table = 'order_packings';
    protected $fillable = [
        'order_dispatch_id',
        'packing_user_id',
        'packing_status',
        'packing_date'
    ];

    protected $auditInclude = [
        'order_dispatch_id',
        'packing_user_id',
        'packing_status',
        'packing_date'
    ];

    public function order_packages() : HasMany
    {
        return $this->hasMany(OrderPackage::class, 'order_packing_id');
    }

    public function order_dispatch() : BelongsTo
    {
        return $this->belongsTo(OrderDispatch::class, 'order_dispatch_id');
    }

    public function packing_user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'packing_user_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as Auditing;

class OrderPackage extends Model implements Auditable
{
    use HasFactory, Auditing;

    protected $table = 'order_packages';
    protected $fillable = [
        'order_packing_id',
        'package_type_id',
        'weight',
        'type_of_package_id',
        'package_date'
    ];

    protected $auditInclude = [
        'order_packing_id',
        'package_type_id',
        'weight',
        'type_of_package_id',
        'package_date'
    ];

    public function order_packing_details() : HasMany
    {
        return $this->hasMany(OrderPackingDetail::class, 'order_package_id');
    }

    public function order_packing() : BelongsTo
    {
        return $this->belongsTo(OrderPacking::class, 'order_packing_id');
    }

    public function type_of_package() : BelongsTo
    {
        return $this->belongsTo(TypeOfPackage::class, 'type_of_package_id');
    }
}

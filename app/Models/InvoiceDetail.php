<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as Auditing;

class InvoiceDetail extends Model implements Auditable
{
    use HasFactory, Auditing;

    protected $table = 'invoice_details';
    protected $fillable = [
        'invoice_id',
        'warehouse_id',
        'product_id',
        'size_id',
        'color_id',
        'quantity',
        'promotion_id',
        'price',
        'discount',
        'subtotal',
        'total'
    ];

    protected $auditInclude = [
        'invoice_id',
        'warehouse_id',
        'product_id',
        'size_id',
        'color_id',
        'quantity',
        'promotion_id',
        'price',
        'discount',
        'subtotal',
        'total'
    ];

    public function invoice_detail_payments() : HasMany
    {
        return $this->hasMany(InvoiceDetailPayment::class, 'invoice_detail_id');
    }

    public function invoice() : BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'invoice_id');
    }

    public function warehouse() : BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function product() : BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function size() : BelongsTo
    {
        return $this->belongsTo(Size::class, 'size_id');
    }

    public function color() : BelongsTo
    {
        return $this->belongsTo(Color::class, 'color_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as Auditing;

class InvoiceDetailPayment extends Model implements Auditable
{
    use HasFactory, Auditing;

    protected $table = 'invoice_detail_payments';
    protected $fillable = [
        'invoice_detail_id',
        'payment_method_id',
        'payment'
    ];

    protected $auditInclude = [
        'invoice_detail_id',
        'payment_method_id',
        'payment'
    ];

    public function invoice_detail() : BelongsTo
    {
        return $this->belongsTo(InvoiceDetail::class, 'invoice_detail_id');
    }

    public function payment_method() : BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
}

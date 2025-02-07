<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as Auditing;

class PaymentMethod extends Model implements Auditable
{
    use HasFactory, Auditing;

    protected $table = 'payment_methods';
    protected $fillable = [
        'name',
        'is_cash',
        'is_libranza',
        'is_transfer',
        'is_card'
    ];

    protected $auditInclude = [
        'name',
        'is_cash',
        'is_libranza',
        'is_transfer',
        'is_card'
    ];

    public function payments() : HasMany
    {
        return $this->hasMany(InvoiceDetailPayment::class, 'payment_method_id');
    }
}

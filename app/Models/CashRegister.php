<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as Auditing;

class CashRegister extends Model implements Auditable
{
    use HasFactory, SoftDeletes, Auditing;

    protected $table = 'cash_registers';
    protected $fillable = [
        'store_id',
        'name',
        'status',
        'user_id'
    ];

    protected $auditInclude = [
        'store_id',
        'name',
        'status',
        'user_id'
    ];

    public function cash_register_controls() : HasMany
    {
        return $this->hasMany(CashRegisterControl::class, 'cash_register_id');
    }

    public function store() : BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

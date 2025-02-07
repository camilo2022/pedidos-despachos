<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as Auditing;

class Invoice extends Model implements Auditable
{
    use HasFactory, Auditing;

    protected $table = 'invoices';
    protected $fillable = [
        'model_id',
        'model_type',
        'reference',
        'status',
        'user_id',
        'cash_register_id'
    ];

    protected $auditInclude = [
        'model_id',
        'model_type',
        'reference',
        'user_id',
        'cash_register_id'
    ];

    public function model() : MorphTo
    {
        return $this->morphTo();
    }

    public function files() : MorphMany
    {
        return $this->morphMany(File::class, 'model');
    }

    public function invoice_details() : HasMany
    {
        return $this->hasMany(InvoiceDetail::class, 'invoice_id');
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cash_register() : BelongsTo
    {
        return $this->belongsTo(CashRegister::class, 'cash_register_id');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('id', 'like', '%' . $search . '%')
        ->orWhere('reference', 'like', '%' . $search . '%');
    }

    public function scopeFilterByDate($query, $start_date, $end_date)
    {
        // Filtro por rango de fechas entre 'start_date' y 'end_date' en el campo 'created_at'
        return $query->whereBetween('created_at', [$start_date, $end_date]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as Auditing;

class Correria extends Model implements Auditable
{
    use HasFactory, SoftDeletes, Auditing;

    protected $table = 'correrias';
    protected $fillable = [
        'name',
        'code',
        'start_date',
        'end_date',
        'business_id'
    ];

    protected $auditInclude = [
        'name',
        'code',
        'start_date',
        'end_date',
        'business_id'
    ];

    public function business() : BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function orders() : HasMany
    {
        return $this->hasMany(Order::class, 'correria_id');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%')
        ->orWhere('code', 'like', '%' . $search . '%')
        ->orWhere('start_date', 'like', '%' . $search . '%')
        ->orWhere('end_date', 'like', '%' . $search . '%');
    }

    public function scopeFilterByDate($query, $start_date, $end_date)
    {
        // Filtro por rango de fechas entre 'start_date' y 'end_date' en el campo 'created_at'
        return $query->whereBetween('created_at', [$start_date, $end_date]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as Auditing;

class Business extends Model implements Auditable
{
    use HasFactory, SoftDeletes, Auditing;

    protected $table = 'businesses';
    protected $fillable = [
        'name',
        'branch',
        'number_document',
        'country',
        'departament',
        'city',
        'address',
        'order_footer',
        'dispatch_footer',
        'packing_footer'
    ];

    protected $auditInclude = [
        'name',
        'branch',
        'number_document',
        'country',
        'departament',
        'city',
        'address',
        'order_footer',
        'order_notify_email',
        'dispatch_footer',
        'packing_footer'
    ];

    public function warehouses() : MorphToMany
    {
        return $this->morphToMany(Warehouse::class, 'model', 'model_warehouses', 'model_id', 'warehouse_id');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('id', 'LIKE', '%' . $search . '%')
        ->orWhere('name', 'LIKE', '%' . $search . '%')
        ->orWhere('branch', 'LIKE', '%' . $search . '%')
        ->orWhere('number_document', 'LIKE', '%' . $search . '%')
        ->orWhere('country', 'LIKE', '%' . $search . '%')
        ->orWhere('departament', 'LIKE', '%' . $search . '%')
        ->orWhere('city', 'LIKE', '%' . $search . '%')
        ->orWhere('address', 'LIKE', '%' . $search . '%');
    }

    public function scopeFilterByDate($query, $start_date, $end_date)
    {
        // Filtro por rango de fechas entre 'start_date' y 'end_date' en el campo 'created_at'
        return $query->whereBetween('created_at', [$start_date, $end_date]);
    }
}

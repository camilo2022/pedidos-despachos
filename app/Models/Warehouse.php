<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as Auditing;

class Warehouse extends Model implements Auditable
{
    use HasFactory, SoftDeletes, Auditing;

    protected $table = 'warehouses';
    protected $fillable = [
        'name',
        'code',
        'type'
    ];

    protected $casts = [
        'type' => 'object'
    ];

    protected $auditInclude = [
        'name',
        'code',
        'type'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'model_warehouses', 'warehouse_id', 'model_id')->where('model_type', User::class);
    }

    public function businesses(): BelongsToMany
    {
        return $this->belongsToMany(Business::class, 'model_warehouses', 'warehouse_id', 'model_id')->where('model_type', Business::class);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('id', 'LIKE', '%' . $search . '%')
        ->orWhere('name', 'LIKE', '%' . $search . '%')
        ->orWhere('code', 'LIKE', '%' . $search . '%');
    }
}

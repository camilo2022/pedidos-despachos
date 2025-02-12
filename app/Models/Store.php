<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as Auditing;

class Store extends Model implements Auditable
{
    use HasFactory, Auditing;

    protected $table = 'stores';
    protected $fillable = [
        'name',
        'document_number',
        'phone_number',
        'address',
        'email',
        'prefix',
        'consecutive',
        'status'
    ];

    protected $auditInclude = [
        'name',
        'document_number',
        'phone_number',
        'address',
        'email',
        'prefix',
        'consecutive',
        'status'
    ];

    public function file() : MorphOne
    {
        return $this->morphOne(File::class, 'model');
    }

    public function warehouses() : MorphToMany
    {
        return $this->morphToMany(Warehouse::class, 'model', 'model_warehouses', 'model_id', 'warehouse_id');
    }
}

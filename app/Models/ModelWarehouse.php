<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as Auditing;

class ModelWarehouse extends Model implements Auditable
{
    use HasFactory, Auditing;

    protected $table = 'model_warehouses';
    protected $fillable = [
        'model_type',
        'model_id',
        'warehouse_id'
    ];

    protected $auditInclude = [
        'model_type',
        'model_id',
        'warehouse_id'
    ];

    public function model() : MorphTo
    {
        return $this->morphTo();
    }

    public function warehouse() : BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }
}

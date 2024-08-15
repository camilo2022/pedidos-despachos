<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as Auditing;

class City extends Model implements Auditable
{
    use HasFactory, Auditing;

    protected $table = 'cities';
    protected $fillable = [
        'departament_id',
        'name',
        'code'
    ];

    protected $auditInclude = [
        'departament_id',
        'name',
        'code'
    ];

    public function departament() : BelongsTo
    {
        return $this->belongsTo(Departament::class, 'departament_id');
    }
}

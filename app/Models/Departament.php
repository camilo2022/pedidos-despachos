<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as Auditing;

class Departament extends Model implements Auditable
{
    use HasFactory, Auditing;

    protected $table = 'departaments';
    protected $fillable = [
        'country_id',
        'name'
    ];

    protected $auditInclude = [
        'country_id',
        'name'
    ];

    public function cities() : HasMany
    {
        return $this->hasMany(City::class, 'departament_id');
    }

    public function country() : BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }


}

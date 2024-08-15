<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as Auditing;

class Country extends Model implements Auditable
{
    use HasFactory, Auditing;

    protected $table = 'countries';
    protected $fillable = [
        'name',
        'tourism_code',
        'country_code'
    ];

    protected $auditInclude = [
        'name',
        'tourism_code',
        'country_code'
    ];

    public function departaments() : HasMany
    {
        return $this->hasMany(Departament::class, 'country_id');
    }
}

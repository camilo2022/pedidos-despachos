<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as Auditing;

class Trademark extends Model implements Auditable
{
    use HasFactory, SoftDeletes, Auditing;

    protected $table = 'warehouses';
    protected $fillable = [
        'name',
        'prefixes'
    ];

    protected $casts = [
        'prefixes' => 'object'
    ];

    protected $auditInclude = [
        'name',
        'prefixes'
    ];

    public function file() : MorphOne
    {
        return $this->morphOne(File::class, 'model');
    }

    public function products() : HasMany
    {
        return $this->hasMany(Product::class, 'trademark_id');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('id', 'LIKE', '%' . $search . '%')
        ->orWhere('name', 'LIKE', '%' . $search . '%');
    }
}

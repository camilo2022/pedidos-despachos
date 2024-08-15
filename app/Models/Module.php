<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as Auditing;

class Module extends Model implements Auditable
{
    use HasFactory, HasRoles, Auditing;

    protected $table = 'modules';
    protected $guard_name = 'item';
    protected $fillable = [
        'name',
        'type',
        'icon'
    ];

    protected $auditInclude = [
        'name',
        'type',
        'icon'
    ];

    protected $auditEvents = [
        'created',
        'updated',
        'deleted',
        'retored'
    ];

    public function submodules() : HasMany
    {
        return $this->hasMany(Submodule::class, 'module_id');
    }

    public function roles() : MorphToMany
    {
        return $this->morphToMany(Role::class, 'model', 'model_has_roles', 'model_id', 'role_id');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('id', 'like',  '%' . $search . '%')
        ->orWhere('name', 'like',  '%' . $search . '%')
        ->orWhere('icon', 'like', '%' . $search . '%')
        ->orWhereHas('roles',
            function ($subQuery) use ($search) {
                $subQuery->where('id', 'like',  '%' . $search . '%')
                ->orWhere('name', 'like',  '%' . $search . '%');
            }
        )
        ->orWhereHas('submodules',
            function ($subQuery) use ($search) {
                $subQuery->where('id', 'like',  '%' . $search . '%')
                ->orWhere('name', 'like',  '%' . $search . '%')
                ->orWhere('url', 'like',  '%' . $search . '%')
                ->orWhere('icon', 'like',  '%' . $search . '%');
            }
        )
        ->orWhereHas('submodules.permission',
            function ($subQuery) use ($search) {
                $subQuery->where('id', 'like',  '%' . $search . '%')
                ->orWhere('name', 'like',  '%' . $search . '%');
            }
        );
    }

    public function scopeFilterByDate($query, $start_date, $end_date)
    {
        // Filtro por rango de fechas entre 'start_date' y 'end_date' en el campo 'created_at'
        return $query->whereBetween('created_at', [$start_date, $end_date]);
    }

    /* public function syncRoles($roles)
    {
        $this->roles()->sync($roles);
    } */
}

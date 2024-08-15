<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as Auditing;

class User extends Authenticatable implements Auditable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes, CanResetPasswordTrait, Auditing;

    protected $table = 'users';
    protected $fillable = [
        'name',
        'last_name',
        'document_number',
        'phone_number',
        'address',
        'email',
        'password',
        'title',
        'zone',
        'business_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $auditInclude = [
        'name',
        'last_name',
        'document_number',
        'phone_number',
        'address',
        'email',
        'password',
        'title',
        'zone',
        'business_id'
    ];

    public function warehouses() : MorphToMany
    {
        return $this->morphToMany(Warehouse::class, 'model', 'model_warehouses', 'model_id', 'warehouse_id');
    }

    public function business() : BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('id', 'LIKE', '%' . $search . '%')
            ->orWhere('name', 'LIKE', '%' . $search . '%')
            ->orWhere('last_name', 'LIKE', '%' . $search . '%')
            ->orWhere('document_number', 'LIKE', '%' . $search . '%')
            ->orWhere('phone_number', 'LIKE', '%' . $search . '%')
            ->orWhere('address', 'LIKE', '%' . $search . '%')
            ->orWhere('email', 'LIKE', '%' . $search . '%')
            ->orWhere('title', 'LIKE', '%' . $search . '%')
            ->orWhereHas('business',
                function ($subQuery) use ($search) {
                    $subQuery->where('id', 'LIKE', '%' . $search . '%')
                        ->orWhere('name', 'LIKE',  '%' . $search . '%');
                }
            );
    }

    public function scopeFilterByDate($query, $start_date, $end_date)
    {
        return $query->whereBetween('created_at', [$start_date, $end_date]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as Auditing;

class Person extends Model implements Auditable
{
    use HasFactory, SoftDeletes, Auditing;

    protected $table = 'people';
    protected $fillable = [
        'client_number_document',
        'type',
        'name',
        'last_name',
        'phone_number',
        'email'
    ];

    protected $auditInclude = [
        'number_document',
        'type',
        'name',
        'last_name',
        'phone_number',
        'email'
    ];

    public function clients() : HasMany
    {
        return $this->hasMany(Client::class, 'client_number_document', 'number_document');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as Auditing;

class Employee extends Model implements Auditable
{
    use HasFactory, SoftDeletes, Auditing;

    protected $table = 'employees';
    protected $fillable = [
        'person_id',
        'gender',
        'birth_date',
        'department',
        'position',
        'blood_type',
        'arl',
        'eps',
        'pension_fund',
        'compensation_fund',
        'admission_date',
        'termination_date',
        'shift',
        'quota'
    ];

    protected $auditInclude = [
        'person_id',
        'gender',
        'birth_date',
        'department',
        'position',
        'blood_type',
        'arl',
        'eps',
        'pension_fund',
        'compensation_fund',
        'admission_date',
        'termination_date',
        'shift',
        'quota'
    ];

    public function person() : BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }
}

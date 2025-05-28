<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as Auditing;

class ClothingLine extends Model implements Auditable
{
    use HasFactory, Auditing;

    protected $table = 'clothing_lines';
    protected $fillable = [
        'name',
        'code'
    ];

    protected $auditInclude = [
        'name',
        'code'
    ];
}

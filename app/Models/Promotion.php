<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as Auditing;

class Promotion extends Model implements Auditable
{
    use HasFactory, Auditing;

    protected $table = 'promotions';
    protected $fillable = [
        'name',
        'apply_trademark',
        'apply_category',
        'apply_product',
        'apply_quantity',
        'apply_percentage',
        'settings'
    ];

    protected $casts = [
        'apply_trademark' => 'boolean',
        'apply_category' => 'boolean',
        'apply_product' => 'boolean',
        'apply_quantity' => 'boolean',
        'apply_percentage' => 'boolean',
        'settings' => 'object'
    ];

    protected $auditInclude = [
        'name',
        'apply_trademark',
        'apply_category',
        'apply_product',
        'apply_quantity',
        'apply_percentage',
        'settings'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as DBModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as Auditing;

class Product extends DBModel implements Auditable
{
    use HasFactory, SoftDeletes, Auditing;

    protected $table = 'products';
    protected $fillable = [
        'item',
        'code',
        'category',
        'trademark',
        'price',
        'description'
    ];

    protected $auditInclude = [
        'item',
        'code',
        'category',
        'trademark',
        'price',
        'description'
    ];

    public function files() : MorphMany
    {
      return $this->morphMany(File::class, 'model');
    }

    public function inventories() : HasMany
    {
        return $this->hasMany(Inventory::class, 'product_id');
    }

    public function order_details() : HasMany
    {
        return $this->hasMany(OrderDetail::class, 'product_id');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('id', 'LIKE', '%' . $search . '%')
        ->orWhere('item', 'LIKE', '%' . $search . '%')
        ->orWhere('code', 'LIKE', '%' . $search . '%')
        ->orWhere('category', 'LIKE', '%' . $search . '%')
        ->orWhere('trademark', 'LIKE', '%' . $search . '%')
        ->orWhere('price', 'LIKE', '%' . $search . '%')
        ->orWhere('description', 'LIKE', '%' . $search . '%')
        ->orWhereHas('inventories.color',
            function ($subQuery) use ($search) {
                $subQuery->where('id', 'LIKE', '%' . $search . '%')
                    ->orWhere('name', 'LIKE',  '%' . $search . '%')
                    ->orWhere('code', 'LIKE',  '%' . $search . '%');
            }
        )
        ->orWhereHas('inventories.size',
            function ($subQuery) use ($search) {
                $subQuery->where('id', 'LIKE', '%' . $search . '%')
                    ->orWhere('name', 'LIKE',  '%' . $search . '%')
                    ->orWhere('code', 'LIKE',  '%' . $search . '%')
                    ->orWhere('description', 'LIKE',  '%' . $search . '%');
            }
        )
        ->orWhereHas('inventories.warehouse',
            function ($subQuery) use ($search) {
                $subQuery->where('id', 'LIKE', '%' . $search . '%')
                    ->orWhere('name', 'LIKE',  '%' . $search . '%')
                    ->orWhere('code', 'LIKE',  '%' . $search . '%');
            }
        );
    }

    public function scopeFilterByDate($query, $start_date, $end_date)
    {
        return $query->whereBetween('created_at', [$start_date, $end_date]);
    }
}

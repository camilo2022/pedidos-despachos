<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as Auditing;

class Order extends Model implements Auditable
{
    use HasFactory, Auditing;

    protected $table = 'orders';
    protected $fillable = [
        'client_id',
        'dispatch_type',
        'dispatch_date',
        'seller_user_id',
        'seller_status',
        'seller_date',
        'seller_observation',
        'seller_dispatch_official',
        'seller_dispatch_document',
        'wallet_user_id',
        'wallet_status',
        'wallet_date',
        'wallet_observation',
        'wallet_dispatch_official',
        'wallet_dispatch_document',
        'dispatch_status',
        'dispatch_date',
        'correria_id',
        'business_id'
    ];

    protected $auditInclude = [
        'client_id',
        'dispatch_type',
        'dispatch_date',
        'seller_user_id',
        'seller_status',
        'seller_date',
        'seller_observation',
        'seller_dispatch_official',
        'seller_dispatch_document',
        'wallet_user_id',
        'wallet_status',
        'wallet_date',
        'wallet_observation',
        'wallet_dispatch_official',
        'wallet_dispatch_document',
        'dispatch_status',
        'dispatched_date',
        'correria_id',
        'business_id'
    ];

    public function order_details() : HasMany
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }

    public function order_dispatches() : HasMany
    {
        return $this->hasMany(OrderDispatch::class, 'order_id');
    }

    public function client() : BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function seller_user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_user_id');
    }

    public function wallet_user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'wallet_user_id');
    }

    public function dispatch_user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'dispatch_user_id');
    }

    public function correria() : BelongsTo
    {
        return $this->belongsTo(Correria::class, 'correria_id');
    }

    public function business() : BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('id', 'LIKE', '%' . $search . '%')
        ->orWhereHas('client',
            function ($subQuery) use ($search) {
                $subQuery->where('id', 'LIKE', '%' . $search . '%')
                ->orWhere('client_name', 'LIKE', '%' . $search . '%')
                ->orWhere('client_address', 'LIKE', '%' . $search . '%')
                ->orWhere('client_number_document', 'LIKE', '%' . $search . '%')
                ->orWhere('client_number_phone', 'LIKE', '%' . $search . '%')
                ->orWhere('client_branch_code', 'LIKE', '%' . $search . '%')
                ->orWhere('client_branch_name', 'LIKE', '%' . $search . '%')
                ->orWhere('client_branch_address', 'LIKE', '%' . $search . '%')
                ->orWhere('client_branch_number_phone', 'LIKE', '%' . $search . '%')
                ->orWhere('departament', 'LIKE', '%' . $search . '%')
                ->orWhere('city', 'LIKE', '%' . $search . '%')
                ->orWhere('number_phone', 'LIKE', '%' . $search . '%')
                ->orWhere('email', 'LIKE', '%' . $search . '%')
                ->orWhere('zone', 'LIKE', '%' . $search . '%');
            }
        )
        ->orWhere('dispatch_type', 'LIKE', '%' . $search . '%')
        ->orWhere('dispatch_date', 'LIKE', '%' . $search . '%')
        ->orWhereHas('seller_user',
            function ($subQuery) use ($search) {
                $subQuery->where('id', 'LIKE', '%' . $search . '%')
                ->orWhere('name', 'LIKE',  '%' . $search . '%');
            }
        )
        ->orWhere('seller_status', 'LIKE', '%' . $search . '%')
        ->orWhere('seller_date', 'LIKE', '%' . $search . '%')
        ->orWhere('seller_observation', 'LIKE', '%' . $search . '%')
        ->orWhereHas('wallet_user',
            function ($subQuery) use ($search) {
                $subQuery->where('id', 'LIKE', '%' . $search . '%')
                ->orWhere('name', 'LIKE',  '%' . $search . '%');
            }
        )
        ->orWhere('wallet_status', 'LIKE', '%' . $search . '%')
        ->orWhere('wallet_date', 'LIKE', '%' . $search . '%')
        ->orWhere('wallet_observation', 'LIKE', '%' . $search . '%')
        ->orWhere('dispatch_status', 'LIKE', '%' . $search . '%')
        ->orWhere('dispatched_date', 'LIKE', '%' . $search . '%')
        ->orWhereHas('correria',
            function ($subQuery) use ($search) {
                $subQuery->where('id', 'LIKE', '%' . $search . '%')
                ->orWhere('name', 'LIKE',  '%' . $search . '%');
            }
        );
    }

    public function scopeFilterByDate($query, $start_date, $end_date)
    {
        // Filtro por rango de fechas entre 'start_date' y 'end_date' en el campo 'created_at'
        return $query->whereBetween('created_at', [$start_date, $end_date]);
    }
}

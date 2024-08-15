<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as Auditing;

class OrderDispatch extends Model implements Auditable
{
    use HasFactory, Auditing;

    protected $table = 'order_dispatches';
    protected $fillable = [
        'client_id',
        'consecutive',
        'dispatch_user_id',
        'dispatch_status',
        'dispatch_date',
        'invoice_user_id',
        'invoice_date',
        'correria_id',
        'business_id'
    ];

    protected $auditInclude = [
        'client_id',
        'consecutive',
        'dispatch_user_id',
        'dispatch_status',
        'dispatch_date',
        'invoice_user_id',
        'invoice_date',
        'correria_id',
        'business_id'
    ];

    public function order_picking() : HasOne
    {
        return $this->hasOne(OrderPicking::class, 'order_dispatch_id');
    }

    public function order_packing() : HasOne
    {
        return $this->hasOne(OrderPacking::class, 'order_dispatch_id');
    }

    public function invoices() : MorphMany
    {
        return $this->morphMany(Invoice::class, 'model');
    }

    public function order_dispatch_details() : HasMany
    {
        return $this->hasMany(OrderDispatchDetail::class, 'order_dispatch_id');
    }

    public function client() : BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function dispatch_user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'dispatch_user_id');
    }

    public function invoice_user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'invoice_user_id');
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
        return $query->where('dispatch_status', 'like',  '%' . $search . '%')
        ->orWhere('dispatch_date', 'like',  '%' . $search . '%')
        ->orWhere('consecutive', 'like',  '%' . $search . '%')
        ->orWhere('payment_status', 'like',  '%' . $search . '%')
        ->orWhere('invoice_date', 'like',  '%' . $search . '%')
        ->orWhereHas('dispatch_user',
            function ($subQuery) use ($search) {
                $subQuery->where('id', 'like', '%' . $search . '%')
                ->orWhere('name', 'like',  '%' . $search . '%')
                ->orWhere('last_name', 'like',  '%' . $search . '%');
            }
        )
        ->orWhereHas('invoice_user',
            function ($subQuery) use ($search) {
                $subQuery->where('id', 'like', '%' . $search . '%')
                ->orWhere('name', 'like',  '%' . $search . '%')
                ->orWhere('last_name', 'like',  '%' . $search . '%');
            }
        )
        ->orWhereHas('order',
            function ($subQuery) use ($search) {
                $subQuery->where('id', 'like', '%' . $search . '%')
                ->orWhereHas('client',
                    function ($subQuery) use ($search) {
                        $subQuery->where('id', 'like', '%' . $search . '%')
                        ->orWhere('name', 'like',  '%' . $search . '%')
                        ->orWhere('document_number', 'like',  '%' . $search . '%');
                    }
                )
                ->orWhereHas('client_branch',
                    function ($subQuery) use ($search) {
                        $subQuery->where('id', 'like', '%' . $search . '%')
                        ->orWhere('name', 'like',  '%' . $search . '%')
                        ->orWhere('code', 'like',  '%' . $search . '%');
                    }
                )
                ->orWhereHas('transporter',
                    function ($subQuery) use ($search) {
                        $subQuery->where('id', 'like', '%' . $search . '%')
                        ->orWhere('name', 'like',  '%' . $search . '%');
                    }
                )
                ->orWhereHas('sale_channel',
                    function ($subQuery) use ($search) {
                        $subQuery->where('id', 'like', '%' . $search . '%')
                        ->orWhere('name', 'like',  '%' . $search . '%');
                    }
                )
                ->orWhere('dispatch', 'like', '%' . $search . '%')
                ->orWhere('dispatch_date', 'like', '%' . $search . '%')
                ->orWhereHas('seller_user',
                    function ($subQuery) use ($search) {
                        $subQuery->where('id', 'like', '%' . $search . '%')
                        ->orWhere('name', 'like',  '%' . $search . '%')
                        ->orWhere('last_name', 'like',  '%' . $search . '%');
                    }
                )
                ->orWhere('seller_status', 'like', '%' . $search . '%')
                ->orWhere('seller_date', 'like', '%' . $search . '%')
                ->orWhere('seller_observation', 'like', '%' . $search . '%')
                ->orWhereHas('wallet_user',
                    function ($subQuery) use ($search) {
                        $subQuery->where('id', 'like', '%' . $search . '%')
                        ->orWhere('name', 'like',  '%' . $search . '%')
                        ->orWhere('last_name', 'like',  '%' . $search . '%');
                    }
                )
                ->orWhere('wallet_status', 'like', '%' . $search . '%')
                ->orWhere('wallet_date', 'like', '%' . $search . '%')
                ->orWhere('wallet_observation', 'like', '%' . $search . '%')
                ->orWhere('dispatched_status', 'like', '%' . $search . '%')
                ->orWhere('dispatched_date', 'like', '%' . $search . '%')
                ->orWhereHas('correria',
                    function ($subQuery) use ($search) {
                        $subQuery->where('id', 'like', '%' . $search . '%')
                        ->orWhere('name', 'like',  '%' . $search . '%');
                    }
                );
            }
        );
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as Auditing;

class Libranza extends Model implements Auditable
{
    use HasFactory, Auditing;

    protected $table = 'libranzas';
    protected $fillable = [
        'invoice_id',
        'value',
        'status',
        'status',
        'code',
        'sms',
        'share',
        'user_id'
    ];

    protected $auditInclude = [
        'invoice_id',
        'value',
        'status',
        'status',
        'code',
        'sms',
        'share',
        'user_id'
    ];

    public function libranza_discounts() : HasMany
    {
        return $this->hasMany(Libranza::class, 'libranza_id');
    }

    public function invoice() : BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

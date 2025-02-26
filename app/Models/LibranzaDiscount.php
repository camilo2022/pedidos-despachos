<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as Auditing;

class LibranzaDiscount extends Model implements Auditable
{
    use HasFactory, Auditing;

    protected $table = 'libranza_discounts';
    protected $fillable = [
        'libranza_id',
        'value',
        'user_id'
    ];

    protected $auditInclude = [
        'libranza_id',
        'value',
        'user_id'
    ];

    public function libranza() : BelongsTo
    {
        return $this->belongsTo(Libranza::class, 'libranza_id');
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as Auditing;

class File extends Model implements Auditable
{
    use HasFactory, SoftDeletes, Auditing;

    protected $table = 'files';
    protected $fillable = [
        'model_id',
        'model_type',
        'type',
        'name',
        'path',
        'mime',
        'extension',
        'size',
        'user_id',
        'metadata'
    ];

    protected $auditInclude = [
        'model_id',
        'model_type',
        'type',
        'name',
        'path',
        'mime',
        'extension',
        'size',
        'user_id',
        'metadata'
    ];

    public function model() : MorphTo
    {
        return $this->morphTo();
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

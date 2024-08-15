<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as Auditing;

class Wallet extends Model implements Auditable
{
    use HasFactory, Auditing;

    protected $table = 'wallets';
    protected $fillable = [
        'number_document',
        'zero_to_thirty',
        'one_to_thirty',
        'thirty_one_to_sixty',
        'sixty_one_to_ninety',
        'ninety_one_to_one_hundred_twenty',
        'one_hundred_twenty_one_to_one_hundred_fifty',
        'one_hundred_fifty_one_to_one_hundred_eighty_one',
        'eldest_to_one_hundred_eighty_one',
        'total'
    ];

    protected $auditInclude = [
        'number_document',
        'zero_to_thirty',
        'one_to_thirty',
        'thirty_one_to_sixty',
        'sixty_one_to_ninety',
        'ninety_one_to_one_hundred_twenty',
        'one_hundred_twenty_one_to_one_hundred_fifty',
        'one_hundred_fifty_one_to_one_hundred_eighty_one',
        'eldest_to_one_hundred_eighty_one',
        'total'
    ];

    public function clients() : HasMany
    {
        return $this->hasMany(Client::class, 'client_number_document', 'number_document');
    }
}

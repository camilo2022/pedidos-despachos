<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as Auditing;

class Client extends Model implements Auditable
{
    use HasFactory, SoftDeletes, Auditing;

    protected $table = 'clients';
    protected $fillable = [
        'client_name',
        'client_address',
        'client_number_document',
        'client_number_phone',
        'client_branch_code',
        'client_branch_name',
        'client_branch_address',
        'client_branch_number_phone',
        'country',
        'departament',
        'city',
        'number_phone',
        'email',
        'zone',
        'type'
    ];

    protected $auditInclude = [
        'client_name',
        'client_address',
        'client_number_document',
        'client_number_phone',
        'client_branch_code',
        'client_branch_name',
        'client_branch_address',
        'client_branch_number_phone',
        'country',
        'departament',
        'city',
        'number_phone',
        'email',
        'zone',
        'type'
    ];

    public function chamber_of_commerce() : MorphOne
    {
        return $this->morphOne(File::class, 'model')->where('type', 'CAMARA DE COMERCIO');
    }

    public function rut() : MorphOne
    {
        return $this->morphOne(File::class, 'model')->where('type', 'RUT');
    }

    public function identity_card() : MorphOne
    {
        return $this->morphOne(File::class, 'model')->where('type', 'DOCUMENTO DE IDENTIFICACION');
    }

    public function signature_warranty() : MorphOne
    {
        return $this->morphOne(File::class, 'model')->where('type', 'FIRMA GARANTIA');
    }

    public function wallet() : HasOne
    {
        return $this->hasOne(Wallet::class, 'number_document', 'client_number_document');
    }

    public function compra() : HasOne
    {
        return $this->hasOne(Person::class, 'client_number_document', 'client_number_document')->where('type', 'COMPRAS');
    }

    public function cartera() : HasOne
    {
        return $this->hasOne(Person::class, 'client_number_document', 'client_number_document')->where('type', 'CARTERA');
    }

    public function bodega() : HasOne
    {
        return $this->hasOne(Person::class, 'client_number_document', 'client_number_document')->where('type', 'BODEGA');
    }

    public function administrador() : HasOne
    {
        return $this->hasOne(Person::class, 'client_number_document', 'client_number_document')->where('type', 'ADMINISTRADOR');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('client_name', 'LIKE', '%' . $search . '%')
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

    public function scopeFilterByDate($query, $start_date, $end_date)
    {
        // Filtro por rango de fechas entre 'start_date' y 'end_date' en el campo 'created_at'
        return $query->whereBetween('created_at', [$start_date, $end_date]);
    }
}

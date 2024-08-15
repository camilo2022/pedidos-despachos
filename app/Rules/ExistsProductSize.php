<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ExistsProductSize implements Rule
{
    public function __construct()
    {
        //
    }

    public function passes($attribute, $value)
    {
        return DB::table('product_sizes')
            ->where('product_id', $value['product_id'])
            ->where('size_id', $value['size_id'])
            ->exists();
    }

    public function message()
    {
        return 'La talla no esta asignada al producto ingresado.';
    }
}

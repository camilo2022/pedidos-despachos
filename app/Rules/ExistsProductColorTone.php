<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ExistsProductColorTone implements Rule
{
    public function __construct()
    {
        //
    }

    public function passes($attribute, $value)
    {
        return DB::table('product_color_tone')
            ->where('product_id', $value['product_id'])
            ->where('color_id', $value['color_id'])
            ->where('tone_id', $value['tone_id'])
            ->exists();
    }

    public function message()
    {
        return 'El color y tono no estan asignados al producto ingresado.';
    }
}

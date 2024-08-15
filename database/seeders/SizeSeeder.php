<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Size::create(['name' => 'TALLA 04', 'code' => '04', 'description' => 'T04']);
        Size::create(['name' => 'TALLA 06', 'code' => '06', 'description' => 'T06']);
        Size::create(['name' => 'TALLA 08', 'code' => '08', 'description' => 'T08']);
        Size::create(['name' => 'TALLA 10', 'code' => '10', 'description' => 'T10']);
        Size::create(['name' => 'TALLA 12', 'code' => '12', 'description' => 'T12']);
        Size::create(['name' => 'TALLA 14', 'code' => '14', 'description' => 'T14']);
        Size::create(['name' => 'TALLA 16', 'code' => '16', 'description' => 'T16']);
        Size::create(['name' => 'TALLA 18', 'code' => '18', 'description' => 'T18']);
        Size::create(['name' => 'TALLA 20', 'code' => '20', 'description' => 'T20']);
        Size::create(['name' => 'TALLA 22', 'code' => '22', 'description' => 'T22']);
        Size::create(['name' => 'TALLA 24', 'code' => '24', 'description' => 'T24']);
        Size::create(['name' => 'TALLA 26', 'code' => '26', 'description' => 'T26']);
        Size::create(['name' => 'TALLA 28', 'code' => '28', 'description' => 'T28']);
        Size::create(['name' => 'TALLA 30', 'code' => '30', 'description' => 'T30']);
        Size::create(['name' => 'TALLA 32', 'code' => '32', 'description' => 'T32']);
        Size::create(['name' => 'TALLA 34', 'code' => '34', 'description' => 'T34']);
        Size::create(['name' => 'TALLA 36', 'code' => '36', 'description' => 'T36']);
        Size::create(['name' => 'TALLA 38', 'code' => '38', 'description' => 'T38']);
        Size::create(['name' => 'TALLA XXS', 'code' => 'XXS', 'description' => 'TXXS']);
        Size::create(['name' => 'TALLA XS', 'code' => 'XS', 'description' => 'TXS']);
        Size::create(['name' => 'TALLA S', 'code' => 'S', 'description' => 'TS']);
        Size::create(['name' => 'TALLA M', 'code' => 'M', 'description' => 'TM']);
        Size::create(['name' => 'TALLA L', 'code' => 'L', 'description' => 'TL']);
        Size::create(['name' => 'TALLA XL', 'code' => 'XL', 'description' => 'TXL']);
        Size::create(['name' => 'TALLA XXL', 'code' => 'XXL', 'description' => 'TXXL']);
    }
}

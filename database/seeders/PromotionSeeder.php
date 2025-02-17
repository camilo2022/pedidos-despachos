<?php

namespace Database\Seeders;

use App\Models\Promotion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PromotionSeeder extends Seeder
{
    public function run()
    {
        Promotion::create([
            'name' => '2 PANT x 100.000',
            'apply_trademark' => true,
            'apply_category' => true,
            'apply_product' => false,
            'apply_quantity' => true,
            'apply_percentage' => false,
            'settings' => json_encode([
                'quantity' => 2,
                'value' => 100000,
                'percentage' => null,
                'trademarks' => ['MICHELL VILLAMIZAR'],
                'categories' => ['BOTA RECTA'],
                'products' => [],
            ])
        ]);

        Promotion::create([
            'name' => '50% DESC +6 UND',
            'apply_trademark' => false,
            'apply_category' => false,
            'apply_product' => false,
            'apply_quantity' => true,
            'apply_percentage' => true,
            'settings' => json_encode([
                'quantity' => 6,
                'value' => null,
                'percentage' => 50,
                'trademarks' => [],
                'categories' => [],
                'products' => [],
            ])
        ]);
    }
}

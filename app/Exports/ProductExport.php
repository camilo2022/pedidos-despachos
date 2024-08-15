<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProductExport implements FromArray, Responsable, WithHeadings, WithTitle
{
    use Exportable;

    private $products;

    public function __construct($products)
    {
        $this->products = $products;
    }

    public function headings(): array
    {
        return [
            'CODIGO',
            'CATEGORIA',
            'MARCA',
            'PRECIO',
            'DESCRIPCION'
        ];
    }

    public function title(): string
    {
        return 'PRODUCTOS';
    }

    public function array(): array
    {
        return $this->products->toArray();
    }
}

<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class InventoryExport implements FromArray, Responsable, WithHeadings, WithTitle
{
    use Exportable;

    private $inventories;

    public function __construct($inventories)
    {
        $this->inventories = $inventories;
    }

    public function headings(): array
    {
        return [
            'BODEGA',
            'CODBOD',
            'MARCA',
            'REFERENCIA',
            'COLOR',
            'CODCOL',
            'TALLA',
            'CANTIDAD',
            'SISTEMA'
        ];
    }

    public function title(): string
    {
        return 'INVENTARIOS';
    }

    public function array(): array
    {
        return $this->inventories->toArray();
    }
}

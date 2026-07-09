<?php

namespace App\Imports;

use App\Models\Factura;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\DB;

class FacturaImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Facturas'        => new FacturasSheetImport(),
            'DetalleFacturas' => new DetalleFacturasSheetImport(),
        ];
    }
}


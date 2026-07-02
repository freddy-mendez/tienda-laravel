<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\SimpleExcel\SimpleExcelReader;
use App\Models\Producto;

class FileUploadController extends Controller
{
    //

    public function uploadCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:10240',
        ]);

        $path = $request->file('file')->getRealPath();

        $count = 0;

        SimpleExcelReader::create($path, 'csv')
            ->useDelimiter(';')
            ->getRows()
            ->each(function (array $row) use (&$count) {
                // Save or manipulate data
                Producto::create([
                    'nombre_producto' => $row['nombre'],
                    'descripcion' => $row['descripcion'],
                    'precio_unitario' => $row['precio'],
                    'stock' => $row['stock'],
                    'iva_porcentaje' => $row['iva'],
                ]);
                $count++;
            });

        return response()->json(['status' => 'success', 'message' => "CSV imported successfully! {$count} rows added."]);
    }
}

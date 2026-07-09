<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Producto extends Model
{
    public $primaryKey = 'producto_id';

    public $fillable = [
        'nombre_producto',
        'descripcion',
        'precio_unitario',
        'stock',
        'iva_porcentaje',
        'imagen_path'
    ];

    public $timestamps = false;

    public function facturas()
    {
        return $this->belongsToMany(Factura::class, 'detalle_facturas', 'producto_id', 'factura_id')
            ->withPivot('detalle_id', 'cantidad', 'precio_venta', 'subtotal_linea');
    }

    public function getImagenUrlAttribute()
    {
        $path = $this->imagen_path ?: 'productos/default.png';
        return Storage::disk('public')->url($path);
    }

    protected $appends = ['imagen_url'];

}

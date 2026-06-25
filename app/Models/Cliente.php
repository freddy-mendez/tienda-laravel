<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    //
    public $primaryKey = 'cliente_id';
    
    public $fillable = [
        'tipo_documento',
        'numero_documento',
        'nombre',
        'apellido',
        'telefono',
        'email',
        'direccion',
        'ciudad',
        'fecha_registro',
        'user_id'
    ];

    public $timestamps = false;

    public function facturas()
    {
        return $this->hasMany(Factura::class, 'cliente_id');
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


}

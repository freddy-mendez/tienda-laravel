<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    //
    public $fillable = [
        'nombre',
        'descripcion'
    ];

    public $timestamps = false;


    public function facturas()
    {
        return $this->hasMany(User::class, 'role_id');
    }
}

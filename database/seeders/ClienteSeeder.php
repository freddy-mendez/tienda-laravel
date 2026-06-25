<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Cliente;


class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $role = Role::where('nombre','cliente')->first();
        $user = User::create([
            'name' => 'Freddy Mendez',
            'email' => 'fmendezo@sena.edu.co',
            'password' => bcrypt('12345678'),
            'role_id' => $role->id,
        ]);
        Cliente::create([
            'tipo_documento' => 'CC',
            'numero_documento'=>'234',
  'nombre'=>'Freddy',
  'apellido'=>'Mendez',
  'telefono'=>'6800600',
  'email'=>'fmendezo@sena.edu.co',
  'direccion'=>'Km 6 Auto Florida',
  'ciudad' => 'Florida',
  'user_id' => $user->id
        ]);
    }
}

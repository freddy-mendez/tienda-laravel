<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $role = Role::where('nombre','admin')->first();
        User::create([
            'name' => 'Super User',
            'email' => 'admin@example.com',
            'password' => bcrypt('12345678'),
            'role_id' => $role->id,
        ]);
        
    }
}

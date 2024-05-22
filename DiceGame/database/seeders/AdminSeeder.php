<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // Crear el usuario con los datos validados
    $user = User::create([
        'name' => "Jose",
        'nickname' => "Jose",
        'email' => "jose@mail.com",
        'password' => "password"
    ]);
    $user->assignRole('admin');

    }
}

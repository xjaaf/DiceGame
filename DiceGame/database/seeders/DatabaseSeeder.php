<?php

namespace Database\Seeders;
use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
      /*  // Crear usuario administrador
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'is_admin' => true, // Asumiendo que tienes una columna 'is_admin' en la tabla users
        ]);
*/
        // Crear usuarios normales
        User::factory(10)->create();
    }
}

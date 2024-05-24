<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Game;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminSeeder::class,
        ]);

        // Crear usuarios normales y asignarles el rol 'player'
        $playerRole = Role::where('name', 'player')->first();

        if ($playerRole) {
            // Crear 10 usuarios y asignarles el rol 'player'
            $users = User::factory(9)->create()->each(function ($user) use ($playerRole) {
                $user->assignRole($playerRole);
            });

            // Para cada usuario creado, generar juegos
            $users->each(function ($user) {
                $games = Game::factory(1)->create(['user_id' => $user->id]);
            });
        }
    }
}

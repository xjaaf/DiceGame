<?php

namespace Database\Seeders;
use App\Models\User;
use Spatie\Permission\Models\Role;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        $users = User::factory(10)->create();
        $playerRole = Role::where('name', 'player')->first();

        if ($playerRole) {
            $users->each(function ($user) use ($playerRole) {
                $user->assignRole($playerRole);
            });
        }
    }
}

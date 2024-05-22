<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        Role::updateOrCreate(['name' => 'player', 'guard_name' => 'api']);
        Role::updateOrCreate(['name' => 'player', 'guard_name' => 'web']);
        Role::updateOrCreate(['name' => 'admin', 'guard_name' => 'api']);
        Role::updateOrCreate(['name' => 'admin', 'guard_name' => 'web']);

    }
}

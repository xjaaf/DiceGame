<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        DB::table('roles')->insert([
            'name' => 'player',
            'guard_name' => 'api'
        ]);

        DB::table('roles')->insert([
            'name' => 'player',
            'guard_name' => 'web'
        ]);

        DB::table('roles')->insert([
            'name' => 'admin',
            'guard_name' => 'api'
        ]);

        DB::table('roles')->insert([
            'name' => 'admin',
            'guard_name' => 'web'
        ]);
        



    }
}

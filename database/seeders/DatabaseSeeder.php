<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name' => 'Fabio Andres Ortega Cruz',
            'email' => 'fabioandres.ortega.cr@unifranz.edu.bo',
            'password' => bcrypt('admin'),
        ]);

        $role = Role::firstOrCreate(['name' => 'Super Administrador', 'guard_name' => 'web']);
        $role->syncPermissions(Permission::all());

        $user->assignRole($role);
    }
}
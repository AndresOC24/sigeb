<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create(); // Para crear 10 usuarios con fábrica

        User::create([
            'name' => 'Fabio Andres Ortega Cruz',
            'email' => 'fabioandres.ortega.cr@unifranz.edu.bo',
            'password' => bcrypt('admin'),
        ]);
    }
}
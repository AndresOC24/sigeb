<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Beca;
use App\Models\Carrera;
use App\Models\Gestion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->roles();
        $this->superAdmin();
        $this->datosMaestros();
    }

    private function roles(): void
    {
        foreach (['Super Administrador', 'Encargado General', 'Encargados', 'Becario'] as $name) {
            Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }
    }

    private function superAdmin(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'fabioandres.ortega.cr@unifranz.edu.bo'],
            [
                'name' => 'Fabio Andres Ortega Cruz',
                'password' => bcrypt('admin'),
                'activo' => 1,
            ]
        );

        $rol = Role::where('name', 'Super Administrador')->first();
        $rol->syncPermissions(Permission::all());
        $user->assignRole($rol);
    }

    private function datosMaestros(): void
    {
        $carreras = ['Ingeniería de Sistemas', 'Medicina', 'Derecho', 'Administración', 'Arquitectura', 'Odontología'];
        foreach ($carreras as $c) {
            Carrera::firstOrCreate(['nombre' => $c]);
        }

        $areas = ['TI', 'Marketing', 'Biblioteca', 'Servicios Estudiantiles', 'DAF', 'Secretaria General'];
        foreach ($areas as $a) {
            Area::firstOrCreate(['nombre' => $a]);
        }

        Gestion::firstOrCreate(
            ['nombre' => 'Gestión 2026'],
            ['fecha_inicio' => '2026-01-01', 'fecha_fin' => '2026-12-31']
        );

        Beca::firstOrCreate(
            ['nombre' => 'Beca Apoyo Institucional'],
            [
                'descripcion' => 'Beca por apoyo en áreas institucionales',
                'horas_requeridas' => 360,
                'porcentaje_beca' => 60,
                'tipo_beca' => 'Beca Apoyo Institucional',
            ]
        );
    }
}
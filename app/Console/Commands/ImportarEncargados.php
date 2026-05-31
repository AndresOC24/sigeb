<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class ImportarEncargados extends Command
{
    protected $signature = 'importar:encargados {archivo=colaboradores.csv}';
    protected $description = 'Importa usuarios desde CSV y les asigna rol Encargados';

    public function handle(): int
    {
        $ruta = storage_path('app/' . $this->argument('archivo'));
        if (! file_exists($ruta)) {
            $this->error("Archivo no encontrado: $ruta");
            return 1;
        }

        $rol = Role::where('name', 'Encargados')->first();
        if (! $rol) {
            $this->error('Rol "Encargados" no existe.');
            return 1;
        }

        $handle = fopen($ruta, 'r');
        fgetcsv($handle); // descarta cabecera

        $creados = 0;
        $existentes = 0;

        while (($fila = fgetcsv($handle)) !== false) {
            [$nombre, $email] = array_map('trim', $fila);

            if (empty($email)) continue;

            if (User::where('email', $email)->exists()) {
                $existentes++;
                continue;
            }

            $user = User::create([
                'name'     => $nombre,
                'email'    => $email,
                'password' => bcrypt('admin'),
                'activo'   => 1,
            ]);
            $user->assignRole($rol);
            $creados++;
        }

        fclose($handle);

        $this->info("Creados: $creados. Ya existían: $existentes.");
        return 0;
    }
}
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registro_asistencia', function (Blueprint $table) {
            // Ruta del documento de evidencia en el disco privado 'local'.
            $table->string('evidencia')->nullable()->after('actividad_principal');
            // Nombre original del archivo, para ofrecerlo tal cual al descargar.
            $table->string('evidencia_nombre')->nullable()->after('evidencia');
            // Cuándo subió el becario la evidencia (puede ser días después de la jornada).
            $table->timestamp('evidencia_subida_en')->nullable()->after('evidencia_nombre');
        });
    }

    public function down(): void
    {
        Schema::table('registro_asistencia', function (Blueprint $table) {
            $table->dropColumn(['evidencia', 'evidencia_nombre', 'evidencia_subida_en']);
        });
    }
};

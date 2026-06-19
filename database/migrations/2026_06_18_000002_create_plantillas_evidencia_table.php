<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Formato general de evidencia que los becarios descargan y adaptan.
        // Se guarda historial: el registro más reciente es el formato vigente.
        Schema::create('plantillas_evidencia', function (Blueprint $table) {
            $table->id();
            $table->string('path');          // ruta en disco privado 'local'
            $table->string('nombre');        // nombre original del archivo
            $table->foreignId('subido_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plantillas_evidencia');
    }
};

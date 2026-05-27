<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asistentes_ayudantia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registro_asistencia_ayudantia_id')->constrained('registro_asistencia_ayudantia')->cascadeOnDelete();
            $table->foreignId('alumno_ayudantia_id')->constrained('alumnos_ayudantia');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistentes_ayudantia');
    }
};

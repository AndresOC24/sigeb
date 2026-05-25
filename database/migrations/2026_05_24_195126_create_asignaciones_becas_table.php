<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asignaciones_becas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('becario_id')->constrained('becarios')->cascadeOnDelete();
            $table->foreignId('beca_id')->constrained('becas')->cascadeOnDelete();
            $table->foreignId('gestion_id')->constrained('gestiones')->cascadeOnDelete();
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->foreignId('jefe_area_id')->constrained('jefe_de_area')->cascadeOnDelete();
            $table->foreignId('materia_id')->constrained('materia')->cascadeOnDelete();
            $table->integer('porcentaje_obtenido')->default(0);
            $table->integer('horas_acumuladas')->default(0);
            $table->enum('estado', ['activa', 'suspendida', 'finalizada'])->default('activa');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignaciones_becas');
    }
};

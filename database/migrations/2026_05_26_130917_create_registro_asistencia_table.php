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
        Schema::create('registro_asistencia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asignacion_beca_id')->constrained('asignaciones_becas');
            $table->foreignId('validado_por')->nullable()->constrained('users');
            $table->date('fecha');
            $table->dateTime('hora_entrada');
            $table->dateTime('hora_salida')->nullable();
            $table->decimal('total_horas', 5, 2)->nullable();
            $table->text('actividad_principal')->nullable(); // solo apoyo institucional, al salir
            $table->text('motivo_rechazo')->nullable();
            $table->boolean('verificado_facial')->default(false);
            $table->decimal('confidence_score', 5, 2)->nullable(); // % 0-100
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registro_asistencia');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('becas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->integer('horas_requeridas')->nullable();
            $table->integer('porcentaje_beca')->nullable();
            $table->enum('tipo_beca', ['Beca ayudantía', 'Beca Apoyo Institucional']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('becas');
    }
};
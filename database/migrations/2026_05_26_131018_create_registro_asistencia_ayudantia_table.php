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
        Schema::create('registro_asistencia_ayudantia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registro_asistencia_id')->unique()->constrained('registro_asistencia')->cascadeOnDelete();
            $table->string('foto_clase', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registro_asistencia_ayudantia');
    }
};

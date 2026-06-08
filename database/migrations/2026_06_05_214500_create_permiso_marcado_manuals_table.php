<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('permisos_marcado_manual', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jefe_de_area_id')->constrained('jefe_de_area')->cascadeOnDelete();
            $table->foreignId('becario_id')->nullable()->constrained('becarios')->cascadeOnDelete();
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin');
            $table->text('motivo');
            $table->foreignId('otorgado_por')->constrained('users');
            $table->boolean('revocado')->default(false);
            $table->timestamps();
            $table->index(
                ['jefe_de_area_id', 'revocado', 'fecha_inicio', 'fecha_fin'],
                'pmm_jefe_vigente_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permisos_marcado_manual');
    }
};
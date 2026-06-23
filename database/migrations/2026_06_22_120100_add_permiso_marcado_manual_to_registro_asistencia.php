<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('registro_asistencia', function (Blueprint $table) {
            // Deja constancia de bajo qué permiso vigente se marcó sin verificación facial.
            $table->foreignId('permiso_marcado_manual_id')->nullable()->after('verificado_facial')
                ->constrained('permisos_marcado_manual')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('registro_asistencia', function (Blueprint $table) {
            $table->dropConstrainedForeignId('permiso_marcado_manual_id');
        });
    }
};

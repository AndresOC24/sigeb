<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('permisos_marcado_manual', function (Blueprint $table) {
            // Workflow de solicitud: el encargado de área la pide, el general la aprueba/rechaza.
            $table->string('estado_solicitud')->default('pendiente')->after('motivo');
            $table->foreignId('solicitado_por')->nullable()->after('estado_solicitud')
                ->constrained('users')->nullOnDelete();
            $table->text('motivo_rechazo')->nullable()->after('solicitado_por');
            $table->dateTime('revisado_en')->nullable()->after('motivo_rechazo');

            // El permiso solo queda otorgado cuando se aprueba.
            $table->foreignId('otorgado_por')->nullable()->change();
        });

        // Las filas existentes fueron creadas bajo el modelo anterior (ya activas):
        // las marcamos como aprobadas para no desactivarlas silenciosamente.
        DB::table('permisos_marcado_manual')->update([
            'estado_solicitud' => 'aprobada',
            'revisado_en' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('permisos_marcado_manual', function (Blueprint $table) {
            $table->dropConstrainedForeignId('solicitado_por');
            $table->dropColumn(['estado_solicitud', 'motivo_rechazo', 'revisado_en']);
        });
    }
};

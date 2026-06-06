<?php

namespace App\Http\Controllers\Becario;

use App\Http\Controllers\Controller;
use App\Models\AsignacionBeca;
use App\Models\RegistroAsistencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AsistenciaFacialController extends Controller
{
    private const THRESHOLD = 0.5;

    public function verificar(Request $request)
    {
        $data = $request->validate([
            'descriptor' => 'required|array|size:128',
            'descriptor.*' => 'numeric',
            'tipo' => 'required|in:entrada,salida',
            'actividad_principal' => 'nullable|string|min:10|max:2000',
        ]);

        $user = Auth::user();
        $rostro = $user->rostro;

        if (! $rostro) {
            return response()->json(['ok' => false, 'message' => 'No tienes un rostro registrado.'], 422);
        }

        $distance = $this->euclideanDistance($rostro->descriptor, $data['descriptor']);

        if ($distance >= self::THRESHOLD) {
            return response()->json([
                'ok' => false,
                'message' => 'Rostro no coincide.',
                'distance' => $distance,
            ], 422);
        }

        $confidence = round(1 - $distance, 4);

        $becario = $user->becario;
        if (! $becario) {
            return response()->json(['ok' => false, 'message' => 'Usuario no es becario.'], 422);
        }

        $asignacion = $becario->asignaciones()->where('estado', 'activa')->first();
        if (! $asignacion) {
            return response()->json(['ok' => false, 'message' => 'No tienes asignación activa.'], 422);
        }

        if ($data['tipo'] === 'entrada') {
            $abierta = RegistroAsistencia::where('asignacion_beca_id', $asignacion->id)
                ->where('fecha', today())
                ->whereNull('hora_salida')
                ->first();

            if ($abierta) {
                return response()->json(['ok' => false, 'message' => 'Ya tienes una jornada abierta.'], 422);
            }

            RegistroAsistencia::create([
                'asignacion_beca_id' => $asignacion->id,
                'fecha' => today(),
                'hora_entrada' => now(),
                'estado' => 'pendiente',
                'verificado_facial' => true,
                'confidence_score' => $confidence,
            ]);

            return response()->json(['ok' => true, 'message' => 'Entrada registrada', 'confidence' => $confidence]);
        }

        // salida
        if (empty($data['actividad_principal'])) {
            return response()->json(['ok' => false, 'message' => 'La actividad principal es obligatoria.'], 422);
        }

        $abierta = RegistroAsistencia::where('asignacion_beca_id', $asignacion->id)
            ->where('fecha', today())
            ->whereNull('hora_salida')
            ->first();

        if (! $abierta) {
            return response()->json(['ok' => false, 'message' => 'No tienes jornada abierta.'], 422);
        }

        $entrada = \Carbon\Carbon::parse($abierta->hora_entrada);
        $salida = now();
        $horas = round($entrada->diffInMinutes($salida) / 60, 2);

        $abierta->update([
            'hora_salida' => $salida,
            'total_horas' => $horas,
            'actividad_principal' => $data['actividad_principal'],
            'verificado_facial' => true,
            'confidence_score' => $confidence,
        ]);

        return response()->json(['ok' => true, 'message' => 'Salida registrada', 'confidence' => $confidence]);
    }

    private function euclideanDistance(array $a, array $b): float
    {
        $sum = 0.0;
        for ($i = 0, $n = count($a); $i < $n; $i++) {
            $d = $a[$i] - $b[$i];
            $sum += $d * $d;
        }
        return sqrt($sum);
    }
}
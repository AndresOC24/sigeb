<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Becario\RostroController;
use App\Http\Controllers\Becario\AsistenciaFacialController;

Route::get('/', function () {
    return redirect('/admin');
});

// Route::get('/becario', function () {
//     return redirect('/admin');
// });

Route::middleware(['auth'])->prefix('becario')->group(function () {
    Route::post('rostro', [RostroController::class, 'store'])->name('becario.rostro.store');
});

Route::post('asistencia/verificar', [AsistenciaFacialController::class, 'verificar'])
    ->name('becario.asistencia.verificar');
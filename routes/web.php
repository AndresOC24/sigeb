<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Becario\RostroController;
use App\Http\Controllers\Becario\AsistenciaFacialController;

Route::get('/', function () {
    $user = auth()->user();

    if ($user instanceof \App\Models\User && ! $user->canAccessPanel(\Filament\Facades\Filament::getPanel('admin'))) {
        return redirect('/becario');
    }

    return redirect('/admin');
});

Route::middleware(['auth'])->prefix('becario')->group(function () {
    Route::post('rostro', [RostroController::class, 'store'])->name('becario.rostro.store');
});

Route::post('asistencia/verificar', [AsistenciaFacialController::class, 'verificar'])
    ->name('becario.asistencia.verificar');
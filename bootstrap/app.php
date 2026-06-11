<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*', headers:
        \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
        \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
        \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT |
        \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Si un usuario autenticado entra a un panel al que no tiene acceso,
        // se le redirige a su panel en lugar de mostrar un 403.
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            if ($e->getStatusCode() !== 403 || $request->expectsJson()) {
                return null;
            }

            $user = $request->user();

            if (! $user instanceof \App\Models\User) {
                return null;
            }

            $admin = \Filament\Facades\Filament::getPanel('admin');
            $becario = \Filament\Facades\Filament::getPanel('becario');

            if (($request->is('admin') || $request->is('admin/*')) && ! $user->canAccessPanel($admin) && $user->canAccessPanel($becario)) {
                return redirect('/becario');
            }

            if (($request->is('becario') || $request->is('becario/*')) && ! $user->canAccessPanel($becario) && $user->canAccessPanel($admin)) {
                return redirect('/admin');
            }

            return null;
        });
    })->create();

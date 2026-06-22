<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Expulsa a un usuario cuya cuenta fue desactivada mientras tenía la
     * sesión abierta. Cierra la sesión y lo redirige al login con un aviso.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $guard = Filament::auth();
        $user = $guard->user();

        if ($user && isset($user->activo) && ! $user->activo) {
            $loginUrl = Filament::getLoginUrl();

            $guard->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            Notification::make()
                ->title('Cuenta desactivada')
                ->body('Tu cuenta ha sido desactivada. Comunícate con el administrador.')
                ->danger()
                ->persistent()
                ->send();

            return redirect()->to($loginUrl);
        }

        return $next($request);
    }
}

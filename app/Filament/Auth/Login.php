<?php

namespace App\Filament\Auth;

use App\Models\User;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    /**
     * Antes del flujo normal de autenticación, si las credenciales
     * corresponden a un usuario desactivado mostramos un mensaje claro
     * en lugar del genérico "estas credenciales no coinciden".
     */
    public function authenticate(): ?LoginResponse
    {
        $email = $this->form->getState()['email'] ?? null;

        if (filled($email)) {
            $user = User::where('email', $email)->first();

            if ($user && ! $user->activo) {
                throw ValidationException::withMessages([
                    'data.email' => 'Tu cuenta ha sido desactivada. Comunícate con el administrador.',
                ]);
            }
        }

        return parent::authenticate();
    }
}

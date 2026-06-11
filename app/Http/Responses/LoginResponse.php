<?php

namespace App\Http\Responses;

use Filament\Auth\Http\Responses\Contracts\LoginResponse as Responsable;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse implements Responsable
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        $panelUrl = Filament::getUrl();
        $intended = session()->pull('url.intended');

        // Solo se respeta la URL pendiente si pertenece al panel donde se
        // inició sesión; una URL de otro panel provocaría un 403.
        if ($intended && str_starts_with($intended, $panelUrl)) {
            return redirect()->to($intended);
        }

        return redirect()->to($panelUrl);
    }
}

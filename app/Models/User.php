<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Database\Factories\UserFactory;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function becario()
    {
        return $this->hasOne(Becario::class);
    }

    public function rostro()
    {
        return $this->hasOne(\App\Models\Rostro::class);
    }


    public function canAccessPanel(Panel $panel): bool
    {
        if ($this->hasRole('Super Administrador')) {
            return true;
        }

        if ($panel->getId() === 'becario') {
            return $this->hasRole('Becario');
        }
        return $this->hasRole(['Encargado General', 'Encargados']);
    }

    public function jefeDeArea()
    {
        return $this->hasOne(\App\Models\JefeDeArea::class);
    }
}

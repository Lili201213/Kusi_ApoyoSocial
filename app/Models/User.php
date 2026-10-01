<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $fillable = ['name', 'usuario', 'email', 'password', 'rol_id', 'activo'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    /** ¿El usuario tiene este permiso? (RF18) */
    public function tienePermiso(string $permiso): bool
    {
        if (! $this->activo || ! $this->rol) {
            return false;
        }

        return $this->rol->permisos->contains('nombre', $permiso);
    }

    public function esAdministrador(): bool
    {
        return $this->rol_id === 1;
    }
}

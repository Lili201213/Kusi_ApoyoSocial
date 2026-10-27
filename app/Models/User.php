<?php

namespace App\Models;

use App\Notifications\RestablecerClave;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    protected $table = 'users';

    protected $fillable = ['name', 'usuario', 'email', 'telefono', 'foto', 'password', 'rol_id', 'activo'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',   // la contraseña se guarda siempre con hash (RNF04)
            'activo' => 'boolean',
            'email_verified_at' => 'datetime',
        ];
    }

    /** Si cambia el correo, se pide verificarlo de nuevo y se envía el enlace */
    protected static function booted(): void
    {
        static::updating(function (User $user) {
            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }
        });

        static::updated(function (User $user) {
            if ($user->wasChanged('email')) {
                $user->sendEmailVerificationNotification();
            }
        });
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

    /** Solo Personal y Supervisor pueden eliminar su propia cuenta; el administrador no */
    public function puedeEliminarCuenta(): bool
    {
        return ! $this->esAdministrador();
    }

    /** URL de la foto de perfil (o null si no tiene) */
    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? asset('uploads/perfiles/'.$this->foto) : null;
    }

    public function getInicialAttribute(): string
    {
        return mb_strtoupper(mb_substr($this->name, 0, 1));
    }

    /** Correo de recuperación de contraseña con la marca de KUSI */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new RestablecerClave($token));
    }
}

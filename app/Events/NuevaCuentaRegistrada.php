<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class NuevaCuentaRegistrada implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public string $nombre;
    public string $usuario;
    public string $rol;
    public string $hora;

    public function __construct(User $user)
    {
        $this->nombre = $user->name;
        $this->usuario = $user->usuario;
        $this->rol = $user->rol->nombre ?? 'Sin rol';
        $this->hora = now()->format('H:i');
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('cuentas')];
    }

    public function broadcastAs(): string
    {
        return 'cuenta.registrada';
    }
}

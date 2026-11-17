<?php

namespace App\Events;

use App\Models\Beneficiario;
use App\Models\Donacion;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class DonacionRegistrada implements ShouldBroadcastNow
{
    use Dispatchable;

    public array $stats;
    public string $hora;

    public function __construct()
    {
        $this->stats = self::estadisticas();
        $this->hora = now()->format('H:i');
    }

    /** Solo números agregados, nada de datos personales */
    public static function estadisticas(): array
    {
        return [
            'donaciones_mes' => Donacion::where('estado', '!=', 'anulada')
                ->whereMonth('fecha_donacion', now()->month)
                ->whereYear('fecha_donacion', now()->year)
                ->count(),
            'donaciones_total' => Donacion::where('estado', '!=', 'anulada')->count(),
            'beneficiarios' => Beneficiario::count(),
        ];
    }

    public function broadcastOn(): array
    {
        return [new Channel('publico')]; // canal público: no pide login
    }

    public function broadcastAs(): string
    {
        return 'donacion.registrada';
    }
}

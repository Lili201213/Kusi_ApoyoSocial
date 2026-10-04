<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donacion extends Model
{
    protected $table = 'donaciones';

    protected $fillable = [
        'donante_id', 'user_id', 'fecha_donacion', 'estado', 'observacion',
        'motivo_anulacion', 'anulada_at', 'anulada_por',
    ];

    protected function casts(): array
    {
        return ['fecha_donacion' => 'date', 'anulada_at' => 'datetime'];
    }

    public function donante()
    {
        return $this->belongsTo(Donante::class, 'donante_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function anuladaPor()
    {
        return $this->belongsTo(User::class, 'anulada_por');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleDonacion::class, 'donacion_id');
    }

    public function estaAnulada(): bool
    {
        return $this->estado === 'anulada';
    }
}

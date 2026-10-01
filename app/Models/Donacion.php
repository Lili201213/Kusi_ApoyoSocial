<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donacion extends Model
{
    protected $table = 'donaciones';

    protected $fillable = [
        'donante_id',
        'user_id',
        'fecha_donacion',
        'observacion',
    ];

    protected function casts(): array
    {
        return ['fecha_donacion' => 'date'];
    }

    public function donante()
    {
        return $this->belongsTo(Donante::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleDonacion::class, 'donacion_id');
    }
}

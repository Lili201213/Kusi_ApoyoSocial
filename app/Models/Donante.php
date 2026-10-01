<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donante extends Model
{
    protected $table = 'donantes';

    protected $fillable = [
        'tipo_persona',
        'nombre_razon_social',
        'documento',
        'telefono',
        'email',
        'direccion',
    ];

    public function donaciones()
    {
        return $this->hasMany(Donacion::class, 'donante_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleDonacion extends Model
{
    protected $table = 'detalle_donaciones';

    protected $fillable = [
        'donacion_id',
        'producto_id',
        'cantidad',
    ];

    public function donacion()
    {
        return $this->belongsTo(Donacion::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    protected $table = 'movimientos_inventario';

    protected $fillable = [
        'producto_id',
        'user_id',
        'tipo',
        'cantidad',
        'fecha',
        'motivo',
        'donacion_id',
        'ayuda_entregada_id',
        'entrega_alimento_id',
    ];

    protected function casts(): array
    {
        return ['fecha' => 'date'];
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function donacion()
    {
        return $this->belongsTo(Donacion::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'categoria_id',
        'nombre',
        'descripcion',
        'unidad_medida',
        'stock_actual',
        'stock_minimo',
        'activo',
    ];

    protected function casts(): array
    {
        return ['stock_actual' => 'decimal:2', 'stock_minimo' => 'decimal:2', 'activo' => 'boolean'];
    }

    public function categoria()
    {
        return $this->belongsTo(CategoriaProducto::class, 'categoria_id');
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoInventario::class, 'producto_id');
    }

    public function getStockBajoAttribute(): bool
    {
        return $this->stock_actual <= $this->stock_minimo;
    }
}

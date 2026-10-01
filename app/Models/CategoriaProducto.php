<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaProducto extends Model
{
    protected $table = 'categorias_producto';

    protected $fillable = [
        'nombre',
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'categoria_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoAyuda extends Model
{
    protected $table = 'tipos_ayuda';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
    ];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function ayudas()
    {
        return $this->hasMany(AyudaEntregada::class, 'tipo_ayuda_id');
    }
}

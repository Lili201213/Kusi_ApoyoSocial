<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AyudaEntregada extends Model
{
    protected $table = 'ayudas_entregadas';

    protected $fillable = [
        'beneficiario_id',
        'tipo_ayuda_id',
        'user_id',
        'fecha_entrega',
        'descripcion',
        'observacion',
    ];

    protected function casts(): array
    {
        return ['fecha_entrega' => 'date'];
    }

    public function beneficiario()
    {
        return $this->belongsTo(Beneficiario::class);
    }

    public function tipoAyuda()
    {
        return $this->belongsTo(TipoAyuda::class, 'tipo_ayuda_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

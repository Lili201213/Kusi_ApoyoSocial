<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NecesidadBeneficiario extends Model
{
    protected $table = 'necesidades_beneficiario';

    protected $fillable = [
        'beneficiario_id',
        'tipo_ayuda_id',
        'fecha_registro',
        'observacion',
        'estado',
    ];

    protected function casts(): array
    {
        return ['fecha_registro' => 'date'];
    }

    public function beneficiario()
    {
        return $this->belongsTo(Beneficiario::class);
    }

    public function tipoAyuda()
    {
        return $this->belongsTo(TipoAyuda::class, 'tipo_ayuda_id');
    }
}

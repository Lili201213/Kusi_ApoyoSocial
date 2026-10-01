<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beneficiario extends Model
{
    protected $table = 'beneficiarios';

    protected $fillable = [
        'dni',
        'nombres',
        'apellidos',
        'fecha_nacimiento',
        'sexo',
        'direccion',
        'telefono',
        'num_familiares',
        'situacion',
        'observaciones',
        'estado',
        'registrado_por',
    ];

    protected function casts(): array
    {
        return ['fecha_nacimiento' => 'date'];
    }

    public function necesidades()
    {
        return $this->hasMany(NecesidadBeneficiario::class, 'beneficiario_id');
    }

    public function ayudas()
    {
        return $this->hasMany(AyudaEntregada::class, 'beneficiario_id');
    }

    public function entregasAlimentos()
    {
        return $this->hasMany(EntregaAlimento::class, 'beneficiario_id');
    }

    public function registrador()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function getNombreCompletoAttribute(): string
    {
        return $this->nombres.' '.$this->apellidos;
    }
}

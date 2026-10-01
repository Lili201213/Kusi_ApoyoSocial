<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntregaAlimento extends Model
{
    protected $table = 'entregas_alimentos';

    protected $fillable = [
        'beneficiario_id',
        'user_id',
        'fecha',
        'tipo_entrega',
        'raciones',
        'observacion',
    ];

    protected function casts(): array
    {
        return ['fecha' => 'date'];
    }

    public function beneficiario()
    {
        return $this->belongsTo(Beneficiario::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

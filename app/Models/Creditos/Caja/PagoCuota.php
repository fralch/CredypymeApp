<?php

namespace App\Models\Creditos\Caja;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagoCuota extends Model
{

    protected $table = 'caja_pago_cuotas';
    protected $primaryKey = 'id';

    protected $fillable = [
        'credito_id',
        'numero_cuota',
        'agencia_caja',
        'caja_id',
        'monto',
        'capital_pagado',
        'interes_pagado',
        'redondeo_pagado',
        'usuario_cobrador',
        'numero_recibo',
        'comentario',
        'pago_banco',
        'banco_id',
        'asesor_id',
        'fecha_pago',

        'datos_creacion',
        'created_at',
        'updated_at'
    ];

    public $timestamps = true;

    protected $casts = ['pago_banco' => 'boolean'];
}

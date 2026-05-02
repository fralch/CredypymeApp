<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComisionPago extends Model
{
    use HasFactory;
    protected $table = 'caja_comisiones_pagos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'desembolso_id',
        'comision_id',
        'monto',
        'agencia_caja',
        'caja_id',

        'fecha_pago',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}

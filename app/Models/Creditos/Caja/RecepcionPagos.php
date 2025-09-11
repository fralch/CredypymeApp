<?php

namespace App\Models\Creditos\Caja;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecepcionPagos extends Model
{
    use HasFactory;
    protected $table = 'caja_recepcion_pagos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'agencia_remitente_id',
        'usuario_remitente',
        'agencia_destinatario_id',
        'caja_destinatario',
        'monto',
        'motivo',
        'concepto',

        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}

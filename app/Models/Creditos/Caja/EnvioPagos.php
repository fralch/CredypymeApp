<?php

namespace App\Models\Creditos\Caja;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnvioPagos extends Model
{
    use HasFactory;
    protected $table = 'caja_envios_pagos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'agencia_remitente_id',
        'caja_remitente',
        'agencia_destinatario_id',
        'usuario_destinatario',
        'monto',
        'motivo',
        'concepto',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}

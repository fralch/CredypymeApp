<?php

namespace App\Models\Creditos\Cuenta;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Envio extends Model
{
    use HasFactory;
    protected $table = 'cuenta_envios';
    protected $primaryKey = 'id';

    protected $fillable = [
        'agencia_remitente_id',
        'remitente_id',
        'agencia_destinatario_id',
        'destinatario_id',
        'monto',
        'concepto',
        'estado',
        'tipo',
        'usuario_gestion_id',
        'entidad_id',
        'comprobante_envio',
        'comprobante_recepcion',
        'comentario_rechazo',
        'datos_creacion',
        'datos_actualizacion',
    ];
}

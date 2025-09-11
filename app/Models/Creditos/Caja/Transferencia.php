<?php

namespace App\Models\Creditos\Caja;

use Illuminate\Database\Eloquent\Model;

class Transferencia extends Model
{
    protected $table = 'caja_transferencias';
    protected $primaryKey = 'id';

    protected $fillable = [
        'tipo',
        'remitente_id',
        'destinatario_id',
        'agencia_id',
        'descripcion',
        'monto',
        'estado',
        'comentario_rechazo',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}

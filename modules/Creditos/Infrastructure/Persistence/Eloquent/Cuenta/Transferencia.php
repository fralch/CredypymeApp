<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Cuenta;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transferencia extends Model
{
    use HasFactory;
    protected $table = 'cuenta_transferencias';
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
        'datos_actualizacion'
    ];

    public $timestamps = true;
}

<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Caja;

use Illuminate\Database\Eloquent\Model;

class PagoNotificacion extends Model
{
    protected $table = 'caja_pago_notificaciones';
    protected $primaryKey = 'id';

    protected $fillable = [
        'notificacion_id',
        'agencia_caja',
        'caja_id',
        'monto',
        'usuario_cobrador',
        'numero_recibo',
        'comentario',
        'pago_banco',
        'banco_id',
        'fecha_pago',

        'datos_creacion',
        'created_at',
        'updated_at'
    ];

    public $timestamps = true;

    protected $casts = ['pago_banco' => 'boolean'];
}

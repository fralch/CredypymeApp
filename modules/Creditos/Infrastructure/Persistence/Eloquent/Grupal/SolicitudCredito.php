<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Grupal;

use Modules\General\Infrastructure\Persistence\Eloquent\Agencia;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes\Grupo;
use Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Credito\Estado;

use Illuminate\Database\Eloquent\Model;

class SolicitudCredito extends Model
{
    protected $table = 'grupo_solicitud_creditos';

    protected $primaryKey = 'id';

    protected $fillable = [
        'grupo_solicitud_id',
        'agencia_cliente',
        'cliente_id',
        'monto',
        'monto_retencion',
        'cuota',
        'estado_id',

        'data_created',
        'data_updated',

        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'monto'           => 'decimal:2',
        'monto_retencion' => 'decimal:2',
        'cuota'           => 'decimal:2',
    ];
}

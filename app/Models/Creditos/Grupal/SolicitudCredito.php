<?php

namespace App\Models\Creditos\Grupal;

use App\Models\General\Agencia;
use App\Models\Creditos\Clientes\Grupo;
use App\Models\Creditos\Mantenimiento\Credito\Estado;

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

<?php

namespace Modules\Logistica\Infrastructure\Persistence\Eloquent\Suministros;

use Illuminate\Database\Eloquent\Model;

class EnvioDetalle extends Model
{
    protected $table = 'suministro_envios_detalles';

    protected $primaryKey = 'id';

    protected $fillable = [
        'envio_id',
        'suministro_id',
        'cantidad',
        'valor_unitario',
        'situacion',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}

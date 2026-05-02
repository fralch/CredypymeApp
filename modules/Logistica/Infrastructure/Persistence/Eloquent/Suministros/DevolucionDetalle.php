<?php

namespace Modules\Logistica\Infrastructure\Persistence\Eloquent\Suministros;

use Illuminate\Database\Eloquent\Model;

class DevolucionDetalle extends Model
{

    protected $table = 'suministro_devoluciones_detalles';
    protected $primaryKey = 'id';
    protected $fillable = [
        'devolucion_id',
        'asignacion_detalle_id',
        'condicion_id',
        'cantidad',
        'valor_unitario',
        'tipo',
        'observacion',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}

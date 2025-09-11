<?php

namespace App\Models\Logistica\Suministros;

use Illuminate\Database\Eloquent\Model;

class AsignacionDetalle extends Model
{
    protected $table = 'suministro_asignaciones_detalles';

    protected $primaryKey = 'id';

    protected $fillable = [
        'asignacion_id',
        'suministro_id',
        'condicion_id',
        'cantidad',
        'cantidad_restante',
        'valor_unitario',
        'estado_id',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'cantidad_devolucion' => 'float',
    ];
}

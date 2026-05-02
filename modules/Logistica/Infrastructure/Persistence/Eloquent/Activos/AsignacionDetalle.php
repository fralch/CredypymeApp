<?php

namespace Modules\Logistica\Infrastructure\Persistence\Eloquent\Activos;

use Illuminate\Database\Eloquent\Model;

class AsignacionDetalle extends Model
{
    protected $table = 'activo_asignaciones_detalles';

    protected $primaryKey = 'id';

    protected $fillable = [
        'asignacion_id',
        'activo_id',
        'condicion_id',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}

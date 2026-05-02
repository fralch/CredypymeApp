<?php

namespace Modules\Gth\Infrastructure\Persistence\Eloquent\Planillas;

use Illuminate\Database\Eloquent\Model;

class VacacionPeriodo extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'planillas_vacaciones_periodos';
    protected $fillable = [
        'dni',
        'periodo_desde',
        'periodo_hasta',
        'acumulado',
        'completado',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}

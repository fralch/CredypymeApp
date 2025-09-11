<?php

namespace App\Models\Gth\Planillas;

use Illuminate\Database\Eloquent\Model;

class VacacionDetalle extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'planillas_vacaciones_detalles';

    protected $fillable = [
        'dni',
        'desde',
        'hasta',
        'periodo_id',
        'dias_tomados',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}

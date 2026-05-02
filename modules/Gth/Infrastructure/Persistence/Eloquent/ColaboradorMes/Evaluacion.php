<?php

namespace Modules\Gth\Infrastructure\Persistence\Eloquent\ColaboradorMes;

use Illuminate\Database\Eloquent\Model;

class Evaluacion extends Model
{
    //
    // public $timestamps = false;
    protected $table = 'colaboradormes_evaluaciones';
    protected $primaryKey = 'id';
    protected $fillable = [
        'examen_id',
        'evaluador_id',
        'evaluado_id',
        'equipo_id',
        'año',
        'mes',
        'culminado',
        'puntuacion_total',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}

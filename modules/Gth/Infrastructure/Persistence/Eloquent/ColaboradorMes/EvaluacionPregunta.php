<?php

namespace Modules\Gth\Infrastructure\Persistence\Eloquent\ColaboradorMes;

use Illuminate\Database\Eloquent\Model;

class EvaluacionPregunta extends Model
{
    //
    //public $timestamps = false;
    protected $table = 'colaboradormes_evaluacion_preguntas';
    protected $primaryKey = 'id';
    protected $fillable = [
        'evaluacion_id',
        'pregunta_id',
        'puntuacion',
        'escala_resultado',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}

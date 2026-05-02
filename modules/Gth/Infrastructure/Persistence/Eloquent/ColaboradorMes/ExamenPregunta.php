<?php

namespace Modules\Gth\Infrastructure\Persistence\Eloquent\ColaboradorMes;

use Illuminate\Database\Eloquent\Model;

class ExamenPregunta extends Model
{
    //
    //public $timestamps = false;
    protected $table = 'colaboradormes_examen_preguntas';
    protected $primaryKey = 'id';
    protected $fillable = [
        'examen_id',
        'pregunta_id',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}

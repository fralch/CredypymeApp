<?php

namespace Modules\Gth\Infrastructure\Persistence\Eloquent\ColaboradorMes;

use Illuminate\Database\Eloquent\Model;

class Pregunta extends Model
{
    //
    //public $timestamps = false;
    protected $table = 'colaboradormes_preguntas';
    protected $primaryKey = 'id';
    protected $fillable = [
        'pregunta',
        'criterio',
        'categoria_id',
        'habilitado',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'

    ];
}

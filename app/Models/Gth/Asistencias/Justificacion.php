<?php

namespace App\Models\Gth\Asistencias;

use Illuminate\Database\Eloquent\Model;

class Justificacion extends Model
{
    //
    protected $table = 'asistencia_justificaciones';
    protected $primaryKey = 'id';
    protected $fillable = [

        'falta_id',
        'tardanza_id',
        'justificacion',
        'documento',
        'datos_creacion',
        'created_at',
        'updated_at'

    ];
}

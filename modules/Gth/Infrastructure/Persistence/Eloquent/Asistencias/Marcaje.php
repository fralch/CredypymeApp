<?php

namespace Modules\Gth\Infrastructure\Persistence\Eloquent\Asistencias;

use Illuminate\Database\Eloquent\Model;

class Marcaje extends Model
{

    protected $table = 'asistencia_marcajes';
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $fillable = [

        'usuario_id',
        'hora_ingreso',
        'turno',
        'foto',
        'hora_salida',

        'coordenadas'
    ];
}

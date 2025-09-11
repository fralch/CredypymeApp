<?php

namespace App\Models\Gth\Mantenimiento\Asistencia;

use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    protected $table = 'asistencia_horarios';
    protected $primaryKey = 'id';
    protected $fillable = [

        'hora_entrada_mañana',
        'hora_salida_mañana',
        'hora_entrada_tarde',
        'hora_salida_tarde',
        'hora_entrada_mañana_s',
        'hora_salida_mañana_s',
        'horario',
        'tolerancia',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}

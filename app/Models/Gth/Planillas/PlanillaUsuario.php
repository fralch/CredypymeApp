<?php

namespace App\Models\Gth\Planillas;

use Illuminate\Database\Eloquent\Model;

class PlanillaUsuario extends Model
{
    //
    protected $primaryKey = 'id_planillas_usuarios';
    protected $table = 'planillas_usuarios';

    protected $fillable = [

        'dni',
        'id_sis_pensiones',
        'hijos',
        'fecha_ingreso',
        'fecha_ingreso_planilla',
        'planilla',
        'cuspp',
        'remuneracion_basica',
        'remuneracion_real',
        'essalud',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}

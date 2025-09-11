<?php

namespace App\Models\Gth\Mantenimiento\Planilla;

use Illuminate\Database\Eloquent\Model;

class SistPension extends Model
{
    //
    protected $primaryKey = 'id_sis_pensiones';
    protected $table = 'planillas_sist_pensiones';
    protected $fillable = [
        'tipo',
        'nombre',
        'tipo_comision',
        'porcentaje',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at',


    ];
}

<?php

namespace App\Models\Gth\Planillas;

use Illuminate\Database\Eloquent\Model;

class PlanillaGeneral extends Model
{
    //
    protected $primaryKey = 'id_planilla';
    protected $table = 'planillas_generales';
    protected $fillable = [
        'dni',
        'dias_computables',
        'dias_vacaciones',
        'dias_faltas',
        'dias_no_laborados',
        'prorrateo_remuneracion_basica',
        'remuneracion_vacaciones',
        'prorrateo_condiciones_trabajo',
        'asig_familiar',
        'riesgo_caja',
        'bonificaciones',
        'comisiones',
        'otras_asignaciones',
        'total_remuneracion_real',
        'total_remuneracion_computable',
        'desc_sistema_pensiones',
        'desc_renta_quinta',
        'adelantos',
        'desc_faltas',
        'desc_tardanzas',
        'desc_otros_riesgo_caja',
        'desc_otros_credito',
        'desc_otros',
        'total_descuentos',
        'total_neto',
        'essalud',
        'mes',
        'año',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'

    ];
}

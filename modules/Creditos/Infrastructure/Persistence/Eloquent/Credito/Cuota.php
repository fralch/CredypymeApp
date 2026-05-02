<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito;

use Illuminate\Database\Eloquent\Model;

class Cuota extends Model
{
    protected $table = 'credito_cuotas';
    protected $primaryKey = 'id';

    protected $fillable = [
        'credito_id',
        'numero_cuota',
        'fecha_vencimiento',
        'dias_atraso',
        'cuota',
        'acumulado',
        'capital',
        'capital_pagado',
        'interes',
        'interes_pagado',
        'redondeo',
        'redondeo_pagado',
        'estado',

        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}

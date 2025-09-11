<?php

namespace App\Models\Creditos\Credito\Records;

use Illuminate\Database\Eloquent\Model;

class CreditoRegistroRecord extends Model
{
    protected $connection = 'records';
    protected $table = 'credito_registros_records';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'credito_id',
        'agencia_id',
        'capital_pagado',
        'interes_pagado',
        'redondeo_pagado',
        'mora_pagado',
        'notificaciones_pagado',
        'dscto_mora_cancelado',
        'dscto_notificaciones_cancelado',
        'dscto_interes_cancelado',
        'saldo_total',
        'dias_atraso',
        'cierre_id',
    ];
}

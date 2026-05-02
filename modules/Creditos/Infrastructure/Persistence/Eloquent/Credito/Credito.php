<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito;

use Illuminate\Database\Eloquent\Model;
use Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios\Usuario;

class Credito extends Model
{
    protected $table = 'credito_registros';
    protected $primaryKey = 'id';

    protected $fillable = [
        'aprobacion_id',
        'grupo_credito_id',
        'fecha_desembolso',
        'agencia_id',
        'cliente_id',
        'asesor_id',
        'cobrador_id',
        'acumulado',
        'saldo_total',
        'capital_total',
        'capital_pagado',
        'interes_total',
        'interes_pagado',
        'redondeo_total',
        'redondeo_pagado',
        'mora_total',
        'mora_pagado',
        'notificaciones_total',
        'notificaciones_pagado',
        'estado_id',
        'calificacion',

        'cuota_actual',
        'cuotas_pendientes',
        'cuotas_vencidas',
        'monto_vencido',
        'fecha_vencimiento',
        'dias_atraso',
        'fecha_ultimo_pago',

        'dscto_mora_cancelado',
        'dscto_notificaciones_cancelado',
        'dscto_interes_cancelado',
        'comentario_cancelado',
        'documento_cancelado',
        'agencia_caja_cancelado',
        'caja_cancelado_id',
        'fecha_hora_cancelado',

        'nueva_empresa',

        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];

    // public function asesor()
    // {
    //     return $this->belongsTo(Usuario::class);
    // }
}

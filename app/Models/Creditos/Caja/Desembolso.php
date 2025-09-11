<?php

namespace App\Models\Creditos\Caja;

use Illuminate\Database\Eloquent\Model;

class Desembolso extends Model
{
    protected $table = 'caja_desembolsos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'credito_id',
        'monto',
        'interes_total',
        'porcentaje_igv',
        'importe_igv',
        'importe_gravado',
        'agencia_caja',
        'caja_id',
        'emite_comprobante',
        'facturado',
        'modo_desembolso',

        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}

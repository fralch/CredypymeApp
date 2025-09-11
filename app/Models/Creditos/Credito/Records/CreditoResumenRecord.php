<?php

namespace App\Models\Creditos\Credito\Records;

use Illuminate\Database\Eloquent\Model;

class CreditoResumenRecord extends Model
{
    protected $connection = 'records';
    protected $table = 'credito_resumen_records';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'asesor_id',
        'agencia_id',
        'tipo_riesgo_id',
        'cantidad',
        'capital_total',
        'saldo_capital',
        'saldo_total',
        'tasa_promedio',
        'tasa_rotacion',
        'porcentaje_tipo',
        'porcentaje_cartera',
        'cantidad_clientes',
        'porcentaje_saldo_total',
        'porcentaje_saldo_cartera',
        'cantidad_clientes_activos',

        'fecha_cartera',
        'cierre_id'
    ];
}

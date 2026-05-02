<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aprobacion extends Model
{
    use HasFactory;
    protected $table = 'credito_aprobaciones';
    protected $primaryKey = 'id';

    protected $fillable = [
        'propuesta_id',
        'numero_credito',
        'numero_credito_2',
        'monto',
        'tasa_interes',
        'plazo',
        'periodo_pago',
        'es_especial',
        'mora_adicional',
        'con_dias_gracia',
        'dias_gracia_ci',
        'dias_gracia_si',
        'cuota',
        'sector_id',
        'producto_id',
        'subproducto_id',
        'tipo_id',
        'pago_oficina',
        'considerado_uno',
        'promotor_id',
        'comentario_aprobacion',
        'estado_id',
        'comentario_anulacion',
        'fecha_aprobacion',
        'comisiones',
        'codigo_seguimiento',
        'codigo_seguimiento_2',

        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];

    protected $casts = ['es_especial' => 'boolean'];
}

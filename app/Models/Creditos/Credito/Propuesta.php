<?php

namespace App\Models\Creditos\Credito;

use Illuminate\Database\Eloquent\Model;

class Propuesta extends Model
{
    protected $table = 'credito_propuestas';
    protected $primaryKey = 'id';

    protected $fillable = [
        'agencia_id',
        'cliente_id',
        'negocio_id',
        'promotor_id',
        'agencia_pariente',
        'pariente_id',
        'agencia_aval',
        'aval_id',
        'agencia_pariente_aval',
        'pariente_aval_id',
        'monto',
        'tasa_interes',
        'plazo',
        'periodo_pago',
        'es_especial',
        'mora_adicional',
        'con_dias_gracia',
        'dias_gracia',
        'cuota',
        'sector_id',
        'producto_id',
        'subproducto_id',
        'tipo_id',
        'pago_oficina',
        'garantia_id',
        'valor_garantia',
        'prendario',
        'prendas',
        'comentario_garantia',
        'comentario_propuesta',
        'comentario_desaprobacion',
        'estado_id',
        'fecha_propuesta',

        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];

    protected $casts = ['es_especial' => 'boolean'];
}

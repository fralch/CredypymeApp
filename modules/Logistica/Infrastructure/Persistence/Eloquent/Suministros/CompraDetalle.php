<?php

namespace Modules\Logistica\Infrastructure\Persistence\Eloquent\Suministros;

use Illuminate\Database\Eloquent\Model;

class CompraDetalle extends Model
{
    protected $table = 'suministro_compras_detalles';

    protected $primaryKey = 'id';

    protected $fillable = [
        'compra_id',
        'suministro_id',
        'cantidad',
        'valor_unitario',
        'valor_total',
        'igv',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}

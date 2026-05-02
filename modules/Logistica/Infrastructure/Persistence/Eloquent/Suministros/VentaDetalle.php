<?php

namespace Modules\Logistica\Infrastructure\Persistence\Eloquent\Suministros;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentaDetalle extends Model
{
    protected $table = 'suministro_ventas_detalles';

    protected $primaryKey = 'id';

    protected $fillable = [
        'venta_id',
        'suministro_id',
        'condicion_id',
        'cantidad',
        'valor_unitario',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}

<?php

namespace Modules\Logistica\Infrastructure\Persistence\Eloquent\Activos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentaDetalle extends Model
{
    protected $table = 'activo_ventas_detalles';

    protected $primaryKey = 'id';

    protected $fillable = [
        'venta_id',
        'activo_id',
        'valor_actual',
        'valor_venta',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}

<?php

namespace App\Models\Logistica\Activos;

use Illuminate\Database\Eloquent\Model;

class CompraDetalle extends Model
{
    protected $table = 'activo_compras_detalles';
    protected $primaryKey = 'id';
    protected $fillable = [
        'compra_id',
        'activo_id',
        'cantidad',
        'valor_unitario',
        'igv',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}

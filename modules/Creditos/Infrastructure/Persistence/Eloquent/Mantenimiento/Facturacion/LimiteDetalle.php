<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Mantenimiento\Facturacion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LimiteDetalle extends Model
{
    use HasFactory;
    protected $table = 'facturacion_limites_detalles';
    protected $primaryKey = 'id';
    protected $fillable = [
        'limite_id',
        'fecha',
        'detalle',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}

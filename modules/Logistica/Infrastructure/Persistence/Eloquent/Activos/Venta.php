<?php

namespace Modules\Logistica\Infrastructure\Persistence\Eloquent\Activos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'activo_ventas';

    protected $primaryKey = 'id';

    protected $fillable = [
        'agencia_id',
        'comprador',
        'documento',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'update_at'
    ];
}

<?php

namespace App\Models\Logistica\Activos;

use Illuminate\Database\Eloquent\Model;

class EnvioDetalle extends Model
{
    protected $table = 'activo_envios_detalles';

    protected $primaryKey = 'id';

    protected $fillable = [
        'envio_id',
        'activo_id',
        'cantidad',
        'valor_actual',
        'situacion',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}

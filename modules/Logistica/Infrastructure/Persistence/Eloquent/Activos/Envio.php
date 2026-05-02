<?php

namespace Modules\Logistica\Infrastructure\Persistence\Eloquent\Activos;

use Illuminate\Database\Eloquent\Model;

class Envio extends Model
{
    protected $table = 'activo_envios';

    protected $primaryKey = 'id';

    protected $fillable = [
        'agencia_envio',
        'agencia_recepcion',
        'responsable_recepcion',
        'ubicacion_recepcion',
        'documento_envio',
        'documento_recepcion',
        'situacion',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}

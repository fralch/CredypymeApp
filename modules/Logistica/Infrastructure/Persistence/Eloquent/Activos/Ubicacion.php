<?php

namespace Modules\Logistica\Infrastructure\Persistence\Eloquent\Activos;

use Illuminate\Database\Eloquent\Model;

class Ubicacion extends Model
{
    protected $table = 'activo_ubicaciones';

    protected $primaryKey = 'id';

    protected $fillable = [
        'ubicacion',
        'abreviacion',
        'descripcion',
        'agencia_id',
        'habilitado',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}

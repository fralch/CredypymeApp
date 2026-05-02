<?php

namespace Modules\Logistica\Infrastructure\Persistence\Eloquent\Activos;

use Illuminate\Database\Eloquent\Model;

class Nombre extends Model
{
    protected $table = 'activo_nombres';

    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre',
        'categoria_id',
        'descripcion',
        'habilitado',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}

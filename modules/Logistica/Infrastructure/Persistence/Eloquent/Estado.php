<?php

namespace Modules\Logistica\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $table = 'logistica_estados';

    protected $primaryKey = 'id';

    protected $fillable = [
        'estado',
        'descripcion',
        'habilitado',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}

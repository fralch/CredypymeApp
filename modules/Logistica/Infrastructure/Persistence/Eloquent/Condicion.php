<?php

namespace Modules\Logistica\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

class Condicion extends Model
{
    protected $table = 'logistica_condiciones';

    protected $primaryKey = 'id';

    protected $fillable = [
        'condicion',
        'descripcion',
        'habilitado',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}

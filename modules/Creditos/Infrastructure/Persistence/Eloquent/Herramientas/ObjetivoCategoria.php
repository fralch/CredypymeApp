<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Herramientas;

use Illuminate\Database\Eloquent\Model;

class ObjetivoCategoria extends Model
{

    protected $table = 'objetivo_categorias';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nivel',
        'minimo',
        'maximo',
        'datos_creacion',
        'datos_actualizacion',

        'created_at',
        'updated_at'
    ];
}

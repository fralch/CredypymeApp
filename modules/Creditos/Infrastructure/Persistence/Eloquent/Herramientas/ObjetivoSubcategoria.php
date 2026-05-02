<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Herramientas;

use Illuminate\Database\Eloquent\Model;

class ObjetivoSubcategoria extends Model
{

    protected $table = 'objetivo_subcategorias';
    protected $primaryKey = 'id';
    protected $fillable = [
        'categoria_id',
        'minimo',
        'maximo',
        'bono',
        'datos_creacion',
        'datos_actualizacion',

        'created_at',
        'updated_at'
    ];
}

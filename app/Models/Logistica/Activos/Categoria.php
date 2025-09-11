<?php

namespace App\Models\Logistica\Activos;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    //
    protected $table = 'activo_categorias';

    protected $primaryKey = 'id';

    protected $fillable = [
        'categoria',
        'descripcion',
        'habilitado',
        'vida_util',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'

    ];
}

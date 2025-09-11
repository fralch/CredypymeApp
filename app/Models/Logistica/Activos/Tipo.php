<?php

namespace App\Models\Logistica\Activos;

use Illuminate\Database\Eloquent\Model;

class Tipo extends Model
{
    protected $table = 'activo_tipos';

    protected $primaryKey = 'id';

    protected $fillable = [
        'tipo',
        'abreviacion',
        'descripcion',
        'habilitado',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}

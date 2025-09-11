<?php

namespace App\Models\Logistica\Suministros;

use Illuminate\Database\Eloquent\Model;

class Tipo extends Model
{
    protected $table = 'suministro_tipos';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'tipo',
        'descripcion',
        'habilitado',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}

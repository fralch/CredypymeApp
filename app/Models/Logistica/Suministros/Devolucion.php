<?php

namespace App\Models\Logistica\Suministros;

use Illuminate\Database\Eloquent\Model;

class Devolucion extends Model
{
    protected $table = 'suministro_devoluciones';
    protected $primaryKey = 'id';
    protected $fillable = [
        'agencia_id',
        'documento',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}

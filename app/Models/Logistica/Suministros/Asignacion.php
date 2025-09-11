<?php

namespace App\Models\Logistica\Suministros;

use Illuminate\Database\Eloquent\Model;

class Asignacion extends Model
{
    protected $table = 'suministro_asignaciones';

    protected $primaryKey = 'id';

    protected $fillable = [
        'agencia_id',
        'responsable_id',
        'documento',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}

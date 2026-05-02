<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Credito;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ef_activo_no_corriente extends Model
{
    use HasFactory;
    protected $table = 'evaluacion_financiera_activo_no_corriente';
    protected $primaryKey = 'id';

    protected $fillable = [
        'evaluacion_id',
        'muebles_enseres',
        'inmueble_maquinaria_equipo',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}

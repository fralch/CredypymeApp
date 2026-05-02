<?php

namespace Modules\Gth\Infrastructure\Persistence\Eloquent\Planillas;

use Illuminate\Database\Eloquent\Model;

class Vacacion extends Model
{
    //
    protected $primaryKey = 'id';
    protected $table = 'planillas_vacaciones';

    protected $fillable = [
        'dni',
        'vencidas',
        'truncadas',
        'indemnizadas',
        'adelantadas',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}

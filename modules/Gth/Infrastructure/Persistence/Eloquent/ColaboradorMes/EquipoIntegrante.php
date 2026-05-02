<?php

namespace Modules\Gth\Infrastructure\Persistence\Eloquent\ColaboradorMes;

use Illuminate\Database\Eloquent\Model;

class EquipoIntegrante extends Model
{
    //
    protected $table = 'colaboradormes_integrantes';
    protected $primaryKey = 'id';
    protected $fillable = [
        'equipo_id',
        'usuario_id',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}

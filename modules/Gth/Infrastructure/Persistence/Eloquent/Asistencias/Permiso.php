<?php

namespace Modules\Gth\Infrastructure\Persistence\Eloquent\Asistencias;

use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    //
    protected $table = 'solicitud_permisos';
    protected $primaryKey = 'id';
    protected $fillable = [
        'fecha_permiso',
        'usuario_id',
        'injustificado',
        'modo',
        'hora_inicio',
        'hora_fin',
        'hora_retorno',
        'tiempo',
        'detalle',
        'documento',
        'aprobador_id',
        'goce',
        'observacion',
        'comentario',
        'validador_id',
        'estado',

        'created_at',
        'updated_at'
    ];
}

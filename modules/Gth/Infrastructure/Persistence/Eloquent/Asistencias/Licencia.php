<?php

namespace Modules\Gth\Infrastructure\Persistence\Eloquent\Asistencias;

use Illuminate\Database\Eloquent\Model;

class Licencia extends Model
{
    //
    protected $table = 'solicitud_licencias';
    protected $primaryKey = 'id';
    protected $fillable = [
        'usuario_id',
        'injustificado',
        'categoria_id',
        'fecha_inicio',
        'fecha_fin',
        'fecha_retorno',
        'dias',
        'detalle',
        'documento',
        'comentario',
        'aprobador_id',
        'goce',
        'observacion',
        'validador_id',
        'estado',
        'created_at',
        'updated_at'
    ];
}

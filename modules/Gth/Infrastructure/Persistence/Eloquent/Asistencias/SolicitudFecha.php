<?php

namespace Modules\Gth\Infrastructure\Persistence\Eloquent\Asistencias;

use Illuminate\Database\Eloquent\Model;

class SolicitudFecha extends Model
{
    //
    public $timestamps = false;
    protected $table = 'solicitud_fechas';
    protected $primaryKey = 'id';
    protected $fillable = [
        'licencia_id',
        'permiso_id',
        'fecha_solicitud',
        'fecha_eliminacion',
        'fecha_aprovacion',
        'fecha_validacion',
        'fecha_verificacion',
        'usuario_ver',

    ];
}

<?php

namespace Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios;

use Illuminate\Database\Eloquent\Model;

class UsuarioHorario extends Model
{
    protected $table = 'asistencia_usuarios_horarios';
    protected $primaryKey = 'id';
    protected $fillable = [

        'usuario_id',
        'horario_id',
        'tolerancia_personal',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'

    ];
    // public $timestamps = false;
}

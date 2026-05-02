<?php

namespace Modules\Gth\Infrastructure\Persistence\Eloquent\Usuarios;

use Illuminate\Database\Eloquent\Model;

class Cesado extends Model
{
    protected $table = 'usuarios_cesados';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'usuario_id',
        'motivo',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
    // public $timestamps = false;
}

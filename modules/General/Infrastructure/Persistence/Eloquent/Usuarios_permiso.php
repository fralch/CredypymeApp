<?php

namespace Modules\General\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

class Usuarios_permiso extends Model
{
    protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $fillable = [
        'usuario_id',
        'permiso_id',
        'datos_creacion',
        'acceso_agencias',
        'datos_actualizacion',
    ];

    // protected $casts = [
    //     'acceso_agencias' => 'array'
    // ];
}

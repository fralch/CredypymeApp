<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $table = 'grupos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'agencia_id',
        'nombre',
        'asesor_id',
        'habilitado',

        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];

    protected $casts = ['habilitado' => 'boolean'];
}

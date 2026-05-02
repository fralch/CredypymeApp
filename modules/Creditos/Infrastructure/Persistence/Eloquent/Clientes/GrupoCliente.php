<?php

namespace Modules\Creditos\Infrastructure\Persistence\Eloquent\Clientes;

use Illuminate\Database\Eloquent\Model;


class GrupoCliente extends Model
{
    protected $table = 'grupo_clientes';
    protected $primaryKey = 'id';

    protected $fillable = [
        'grupo_id',
        'cliente_id',
        'responsable',

        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}

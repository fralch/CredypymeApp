<?php

namespace Modules\Logistica\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

class Responsable extends Model
{
    protected $table = 'logistica_responsables';

    protected $primaryKey = 'id';

    protected $fillable = [
        'responsable',
        'abreviacion',
        'agencia_id',
        'usuario_id',
        'encargado_agencia',
        'descripcion',
        'habilitado',
        'datos_creacion',
        'datos_actualizacion',
        'created_at',
        'updated_at'
    ];
}
